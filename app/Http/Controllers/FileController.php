<?php

namespace App\Http\Controllers;

use App\Models\File;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class FileController extends Controller
{
    public function index()
    {
        return Inertia::render('Files/Index', [
            'files' => File::with(['media', 'codes'])->get()->map(function ($file) {
                return [
                    'id' => $file->id,
                    'title' => $file->title,
                    'thumbnail' => $file->getFirstMediaUrl('thumbnails') ?: null,
                    'filename' => $file->getFirstMedia('files')?->file_name,
                    'size' => $file->file_size,
                    'type' => $file->file_type,
                    'download_count' => $file->download_count,
                    'created_at' => $file->formatted_created_at,
                    'codes' => $file->codes->map(function ($code) {
                        return [
                            'id' => $code->id,
                            'code' => $code->code,
                            'usage_limit' => $code->usage_limit,
                            'usage_count' => $code->usage_count,
                            'expires_at' => $code->expires_at?->format('Y-m-d H:i:s'),
                        ];
                    }),
                ];
            }),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'file' => 'required|file|max:20480', // 20MB max
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // 2MB max for thumbnails
        ]);

        $file = $request->file('file');
        
        // Validate file type
        if (!$this->isValidFileType($file)) {
            throw ValidationException::withMessages([
                'file' => 'Invalid file type. Only MP3 and ZIP files are allowed.',
            ]);
        }

        $fileModel = File::create(['title' => $request->title]);

        // Store main file
        $fileModel->addMedia($file)
                 ->toMediaCollection('files');

        // Handle thumbnail if provided
        if ($request->hasFile('thumbnail')) {
            $fileModel->addMedia($request->file('thumbnail'))
                     ->toMediaCollection('thumbnails');
        }

        return redirect()->route('files.index')->with('success', 'File uploaded successfully.');
    }

    public function destroy(File $file)
    {
        $file->delete();
        return redirect()->route('files.index')->with('success', 'File deleted successfully.');
    }

    public function download(File $file)
    {
        $media = $file->getFirstMedia('files');
        
        if (!$media) {
            return redirect()->back()->with('error', 'File not found.');
        }

        // Track download (optional)
        $file->increment('download_count');

        // Stream the file with proper headers
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

    public function update(Request $request, File $file)
    {
        $request->validate([
            'title' => 'required|string|max:255',
        ]);

        $file->update([
            'title' => $request->title,
        ]);

        return redirect()->route('files.index')->with('success', 'File updated successfully.');
    }

    private function isValidFileType($file)
    {
        $mimeType = $file->getMimeType();
        $content = file_get_contents($file->getRealPath());
        
        // Check for MP3
        if ($file->getClientOriginalExtension() === 'mp3') {
            // Check for MP3 frame synchronization bytes
            $isMp3 = strpos($content, "\xFF\xFB") !== false || 
                     strpos($content, "\xFF\xFA") !== false ||
                     strpos($content, "\xFF\xF3") !== false ||
                     strpos($content, "\xFF\xF2") !== false;
            
            return $isMp3 && str_starts_with($mimeType, 'audio/');
        }
        
        // Check for ZIP
        if ($file->getClientOriginalExtension() === 'zip') {
            // Check for ZIP file signature (PK\x03\x04)
            $isZip = strpos($content, "PK\x03\x04") === 0;
            
            return $isZip && $mimeType === 'application/zip';
        }
        
        return false;
    }
}
