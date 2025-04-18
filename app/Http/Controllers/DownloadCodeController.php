<?php

namespace App\Http\Controllers;

use App\Models\DownloadCode;
use App\Models\File;
use Illuminate\Http\Request;
use Inertia\Inertia;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use League\Csv\Writer;

class DownloadCodeController extends Controller
{
    public function store(Request $request, File $file)
    {
        $request->validate([
            'usage_limit' => 'required|integer|min:1',
            'expires_at' => 'nullable|date|after:now',
        ]);

        $code = DownloadCode::create([
            'code' => strtoupper(substr(str_replace(['+', '/', '='], '', base64_encode(random_bytes(8))), 0, 8)),
            'file_id' => $file->id,
            'usage_limit' => $request->usage_limit,
            'expires_at' => $request->expires_at,
        ]);

        return redirect()->route('files.show', $file)->with('success', 'Code generated.');
    }

    public function redeem(Request $request)
    {
        $request->validate(['code' => 'required|string']);
        
        $code = DownloadCode::where('code', $request->code)
            ->with('file.media')
            ->firstOrFail();

        if ($code->usage_count >= $code->usage_limit || 
            ($code->expires_at && $code->expires_at->isPast())) {
            return back()->withErrors(['code' => 'Invalid or expired code.']);
        }

        $code->increment('usage_count');
        $media = $code->file->getFirstMedia('files');
        
        if (!$media) {
            return back()->withErrors(['code' => 'File not found.']);
        }

        return response()->download($media->getPath(), $media->file_name);
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

    public function generateQrCode(DownloadCode $code)
    {
        $url = route('codes.redeem-form', ['code' => $code->code]);
        $qrCode = QrCode::size(300)->generate($url);
        
        return response($qrCode)->header('Content-Type', 'image/svg+xml');
    }
}
