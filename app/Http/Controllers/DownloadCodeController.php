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

        return redirect()->route('files.index')
            ->with('success', $request->number_of_codes . ' download code(s) generated successfully.');
    }

    /**
     * Generate QR code for a download code
     */
    public function generateQr(DownloadCode $code)
    {
        $svg = $this->downloadCodeService->generateQrCode($code);
        
        return Response::make($svg, 200, [
            'Content-Type' => 'image/svg+xml',
        ]);
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
     * Redeem a download code
     */
    public function redeem(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
        ]);

        $downloadCode = $this->downloadCodeService->validateCode($request->code);

        if (!$downloadCode) {
            throw ValidationException::withMessages([
                'code' => 'Invalid or expired download code.',
            ]);
        }

        // Record the usage
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

        return response($csv->toString(), 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=codes-{$file->id}.csv",
        ]);
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

        if ($request->has('search')) {
            $query->where('code', 'like', '%' . $request->search . '%')
                  ->orWhereHas('file', function ($q) use ($request) {
                      $q->where('title', 'like', '%' . $request->search . '%');
                  });
        }

        $codes = $query->paginate(50)
            ->through(function ($code) {
                return [
                    'id' => $code->id,
                    'code' => $code->code,
                    'usage_limit' => $code->usage_limit,
                    'usage_count' => $code->usage_count,
                    'expires_at' => $code->expires_at?->format('Y-m-d H:i:s'),
                    'created_at' => $code->created_at->format('M j, Y g:i A'),
                    'file' => [
                        'id' => $code->file->id,
                        'title' => $code->file->title,
                    ],
                ];
            });

        return Inertia::render('Codes/AllCodes', [
            'codes' => $codes,
        ]);
    }
}
