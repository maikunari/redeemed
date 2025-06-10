<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\DownloadCode;
use App\Services\DownloadCodeService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use League\Csv\Writer;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Barryvdh\DomPDF\Facade\Pdf;

class DownloadCodeController extends Controller
{
    protected $downloadCodeService;

    public function __construct(DownloadCodeService $downloadCodeService)
    {
        $this->downloadCodeService = $downloadCodeService;
    }

    /**
     * Store a new download code
     */
    public function store(Request $request, File $file)
    {
        $request->validate([
            'number_of_codes' => 'required|integer|min:1|max:100',
            'usage_limit' => 'required|integer|min:1',
            'expires_at' => 'nullable|date|after:now',
        ]);

        $expiresAt = $request->expires_at ? Carbon::parse($request->expires_at) : null;
        
        // Generate multiple codes
        for ($i = 0; $i < $request->number_of_codes; $i++) {
            $this->downloadCodeService->createCode(
                $file,
                $request->usage_limit,
                $expiresAt
            );
        }

        return redirect()->route('codes.index', ['file' => $file->id])
            ->with('success', $request->number_of_codes . ' download code(s) generated successfully.');
    }

    /**
     * Generate QR code for a download code
     */
    public function generateQr(DownloadCode $code)
    {
        try {
            $svg = $this->downloadCodeService->generateQrCode($code);
            
            return Response::make($svg, 200, [
                'Content-Type' => 'image/svg+xml',
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error generating QR code for code ID ' . $code->id . ': ' . $e->getMessage());
            return Response::make('Error generating QR code', 500, [
                'Content-Type' => 'text/plain',
            ]);
        }
    }

    /**
     * Show the code redemption form
     */
    public function showRedeemForm(Request $request)
    {
        return Inertia::render('Redeem/Form', [
            'code' => $request->query('code'),
        ]);
    }

    /**
     * Delete a specific download code
     */
    public function destroy(DownloadCode $code)
    {
        $code->delete();
        return redirect()->back()->with('message', 'Code deleted successfully.');
    }

    /**
     * Update the usage limit of a specific download code
     */
    public function renew(Request $request, DownloadCode $code)
    {
        $request->validate([
            'usage_limit' => 'required|integer|min:1'
        ]);

        $code->update(['usage_limit' => $request->usage_limit]);
        return redirect()->back()->with('message', 'Usage limit updated successfully.');
    }

    /**
     * Redeem a download code
     */
    public function redeem(Request $request)
    {
        $request->validate([
            'code' => ['required', 'string', 'size:6', 'regex:/^[2-9]{6}$/'],
        ]);

        // Add a safeguard to prevent multiple redemption attempts for the same code in a short time
        $redeemKey = 'redeem_attempt_' . $request->code;
        if (\Illuminate\Support\Facades\Cache::has($redeemKey)) {
            throw ValidationException::withMessages([
                'code' => 'Redemption attempt too frequent. Please wait a moment before trying again.',
            ]);
        }
        \Illuminate\Support\Facades\Cache::put($redeemKey, true, now()->addSeconds(5));

        $downloadCode = $this->downloadCodeService->validateCode($request->code);

        if (!$downloadCode) {
            throw ValidationException::withMessages([
                'code' => 'Invalid or expired download code.',
            ]);
        }

        // Record the usage for every valid redemption
        $this->downloadCodeService->recordUsage($downloadCode);

        // Get the file and stream it to the user
        $media = $downloadCode->file->getFirstMedia('files');
        
        if (!$media) {
            throw ValidationException::withMessages([
                'code' => 'File not found.',
            ]);
        }

        return response()->stream(
            function () use ($media) {
                $stream = $media->stream();
                fpassthru($stream);
                if (is_resource($stream)) {
                    fclose($stream);
                }
            },
            200,
            [
                'Content-Type' => $media->mime_type,
                'Content-Length' => $media->size,
                'Content-Disposition' => 'attachment; filename="' . $media->file_name . '"',
                'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
                'Pragma' => 'no-cache',
            ]
        );
    }

    public function export(File $file)
    {
        $csv = Writer::createFromString();
        $csv->insertOne(['Code', 'Usage Limit', 'Usage Count', 'Expires At']);
        
        foreach ($file->codes as $code) {
            $csv->insertOne([
                $code->code,
                $code->usage_limit,
                $code->usage_count,
                $code->expires_at ? $code->expires_at->format('Y-m-d H:i:s') : 'Never'
            ]);
        }

        $safeTitle = str_replace(['/', '\\', '?', '%', '*', ':', '|', '"', '<', '>', '.', ','], '-', $file->title);
        
        return response($csv->toString(), 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$safeTitle} ({$file->codes->count()}).csv\"",
        ]);
    }

    /**
     * Export download codes as PDF business cards
     */
    public function exportCards(Request $request, File $file)
    {
        $request->validate([
            'codes' => 'nullable|array',
            'codes.*' => 'exists:download_codes,id',
        ]);

        // Get codes to export (selected codes or all codes for the file)
        if ($request->has('codes') && !empty($request->codes)) {
            $codes = $file->codes()->whereIn('id', $request->codes)->get();
        } else {
            $codes = $file->codes;
        }

        if ($codes->isEmpty()) {
            return redirect()->back()->withErrors(['codes' => 'No codes found to export.']);
        }

        // Generate QR codes for each download code
        $codesWithQr = $codes->map(function ($code) {
            return [
                'code' => $code->code,
                'file_title' => $code->file->title,
                'redemption_url' => route('codes.redeem-form', ['code' => $code->code]),
                'qr_code' => $this->generateQrCodeData($code),
                'expires_at' => $code->expires_at ? $code->expires_at->format('M j, Y') : null,
                'usage_info' => $code->usage_count . '/' . $code->usage_limit . ' uses',
            ];
        });

        // Generate front side PDF
        $frontPdf = Pdf::loadView('pdf.business-cards-front', [
            'codes' => $codesWithQr,
            'app_name' => config('app.name'),
            'website_url' => config('app.url'),
        ]);
        
        // Configure PDF settings for business cards
        $frontPdf->setPaper([0, 0, 612, 792], 'portrait') // 8.5" x 11" in points
               ->setOptions([
                   'dpi' => 300,
                   'defaultFont' => 'helvetica',
                   'isRemoteEnabled' => true,
               ]);

        // Generate back side PDF
        $backPdf = Pdf::loadView('pdf.business-cards-back', [
            'codes' => $codesWithQr,
            'app_name' => config('app.name'),
        ]);
        
        $backPdf->setPaper([0, 0, 612, 792], 'portrait')
               ->setOptions([
                   'dpi' => 300,
                   'defaultFont' => 'helvetica',
                   'isRemoteEnabled' => true,
               ]);

        $safeTitle = str_replace(['/', '\\', '?', '%', '*', ':', '|', '"', '<', '>', '.', ','], '-', $file->title);
        
        // For now, return the front PDF (we'll enhance this later to handle both sides)
        return $frontPdf->download("{$safeTitle}-cards-front-({$codes->count()}).pdf");
    }

    /**
     * Generate QR code data for a download code
     */
    protected function generateQrCodeData(DownloadCode $code)
    {
        $redemptionUrl = route('codes.redeem-form', ['code' => $code->code]);
        
        return base64_encode(
            QrCode::format('png')
                  ->size(200)
                  ->errorCorrection('M')
                  ->generate($redemptionUrl)
        );
    }

    /**
     * Display the codes management page for a file
     */
    public function index(Request $request, File $file)
    {
        $query = $file->codes();

        if ($request->has('search')) {
            $query->where('code', 'like', '%' . $request->search . '%');
        }

        $codes = $query->orderBy('created_at', 'desc')
            ->paginate(50)
            ->through(function ($code) {
                return [
                    'id' => $code->id,
                    'code' => $code->code,
                    'usage_limit' => $code->usage_limit,
                    'usage_count' => $code->usage_count,
                    'expires_at' => $code->expires_at?->format('Y-m-d H:i:s'),
                    'created_at' => $code->created_at->format('M j, Y g:i A'),
                ];
            });

        return Inertia::render('Codes/Index', [
            'file' => [
                'id' => $file->id,
                'title' => $file->title,
                'filename' => $file->getFirstMedia('files')?->file_name,
                'size' => $file->file_size,
                'type' => $file->file_type,
                'download_count' => $file->download_count,
                'created_at' => $file->formatted_created_at,
            ],
            'codes' => $codes,
        ]);
    }

    /**
     * Display all download codes
     */
    public function allCodes(Request $request)
    {
        $query = DownloadCode::with('file')->orderBy('created_at', 'desc');

        if ($request->has('file') && $request->file !== '') {
            $query->where('file_id', $request->file);
        }

        if ($request->has('search')) {
            $query->where(function($q) use ($request) {
                $q->where('code', 'like', '%' . $request->search . '%')
                  ->orWhereHas('file', function ($q) use ($request) {
                      $q->where('title', 'like', '%' . $request->search . '%');
                  });
            });
        }

        $codes = $query->paginate(50)
            ->through(function ($code) {
                return [
                    'id' => $code->id,
                    'code' => $code->code,
                    'usage_limit' => $code->usage_limit,
                    'usage_count' => $code->usage_count,
                    'expires_at' => $code->expires_at ? $code->expires_at->format('Y-m-d H:i:s') : null,
                    'created_at' => $code->created_at->format('M j, Y g:i A'),
                    'file' => [
                        'id' => $code->file->id,
                        'title' => $code->file->title,
                    ],
                ];
            });

        return Inertia::render('Codes/AllCodes', [
            'codes' => $codes,
            'files' => File::select('id', 'title')->orderBy('title')->get(),
        ]);
    }
}
