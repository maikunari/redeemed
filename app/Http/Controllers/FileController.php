<?php

namespace App\Http\Controllers;

use App\Models\File;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Http\JsonResponse;

class FileController extends Controller
{
    public function index()
    {
        return Inertia::render('Files/Index', [
            'files' => File::with(['media', 'codes'])->get()->map(function ($file) {
                return [
                    'id' => $file->id,
                    'title' => $file->title,
                    'thumbnail' => $file->thumbnail_url,
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
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'file' => 'required|file|max:20480', // 20MB max
                'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // 2MB max for thumbnails
            ]);

            $file = $request->file('file');
            
            // Validate file type
            if (!$this->isValidFileType($file)) {
                return back()->with('error', 'Invalid file type. Only MP3 and ZIP files are allowed.');
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
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors());
        } catch (\Exception $e) {
            if (str_contains($e->getMessage(), 'exceeds the limit')) {
                return back()->with('error', 'The file is too large. Maximum allowed size is 20MB.');
            }
            return back()->with('error', 'An error occurred while uploading the file. Please try again.');
        }
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

    public function updateThumbnail(Request $request, File $file)
    {
        $request->validate([
            'thumbnail' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Remove old thumbnail if it exists
        if ($file->getFirstMedia('thumbnails')) {
            $file->getFirstMedia('thumbnails')->delete();
        }

        // Add new thumbnail
        $file->addMedia($request->file('thumbnail'))
             ->toMediaCollection('thumbnails');

        return redirect()->route('files.index')->with('success', 'Thumbnail updated successfully.');
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

    /**
     * Scan FTP staging directory for uploaded files
     */
    public function scanFtpStaging(): JsonResponse
    {
        try {
            // Call the artisan command to scan FTP staging
            Artisan::call('files:scan-ftp-staging', ['--json' => true]);
            $output = Artisan::output();
            
            $result = json_decode(trim($output), true);
            
            if (!$result) {
                return response()->json([
                    'success' => false,
                    'error' => 'Failed to scan FTP staging directory'
                ], 500);
            }
            
            // Map the data structure to match frontend expectations
            $mappedFiles = collect($result['files'] ?? [])->map(function ($file) {
                return [
                    'filename' => $file['filename'],
                    'title' => $file['final_title'],
                    'size' => $file['size_formatted'],
                    'type' => $file['extension'] === 'mp3' ? 'audio' : 'archive',
                    'valid' => $file['is_valid'],
                    'error' => $file['error']
                ];
            })->toArray();
            
            return response()->json([
                'success' => $result['success'],
                'count' => $result['count'],
                'files' => $mappedFiles
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error scanning FTP staging: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Process selected FTP files
     */
    public function processFtpFiles(Request $request): JsonResponse
    {
        $request->validate([
            'files' => 'required|array|min:1',
            'files.*' => 'required|string'
        ]);

        $filenames = $request->input('files', []);
        
        $results = [
            'success' => true,
            'processed' => [],
            'failed' => [],
            'deleted' => [],
            'conflicts' => 0,
            'total' => count($filenames)
        ];

        $stagingPath = storage_path('app/ftp-staging');
        $processedPath = storage_path('app/ftp-processed');

        foreach ($filenames as $filename) {
            try {
                $filePath = $stagingPath . '/' . $filename;
                
                // Verify file exists
                if (!file_exists($filePath)) {
                    $results['failed'][] = [
                        'filename' => $filename,
                        'error' => 'File not found'
                    ];
                    continue;
                }

                // Validate file type
                $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                if (!$this->isValidFtpFile($filePath, $extension)) {
                    // Delete invalid file
                    unlink($filePath);
                    $results['deleted'][] = [
                        'filename' => $filename,
                        'reason' => 'Invalid file type'
                    ];
                    continue;
                }

                // Generate unique title
                $baseTitle = pathinfo($filename, PATHINFO_FILENAME);
                $finalTitle = $this->generateUniqueTitle($baseTitle);
                
                // Track conflicts
                if ($finalTitle !== $baseTitle) {
                    $results['conflicts']++;
                }

                // Create File model
                $fileModel = File::create(['title' => $finalTitle]);

                // Copy file to processed folder first (before media library consumes it)
                $processedFilePath = $processedPath . '/' . date('Y-m-d_H-i-s_') . $filename;
                copy($filePath, $processedFilePath);

                // Move file to Spatie media library (this will consume the original file)
                $fileModel->addMedia($filePath)->toMediaCollection('files');

                $results['processed'][] = [
                    'filename' => $filename,
                    'title' => $finalTitle,
                    'file_id' => $fileModel->id
                ];

            } catch (\Exception $e) {
                // Log the error for debugging
                \Log::error('FTP Processing Error for ' . $filename . ': ' . $e->getMessage());
                \Log::error('Stack trace: ' . $e->getTraceAsString());
                
                $results['failed'][] = [
                    'filename' => $filename,
                    'error' => $e->getMessage()
                ];
            }
        }

        // Map results to match frontend expectations
        $response = [
            'success' => count($results['failed']) === 0,
            'processed' => count($results['processed']),
            'invalid' => count($results['deleted']),
            'conflicts' => $results['conflicts'],
            'errors' => collect($results['failed'])->pluck('error')->toArray(),
            'details' => [
                'processed_files' => $results['processed'],
                'deleted_files' => $results['deleted'],
                'failed_files' => $results['failed']
            ]
        ];

        return response()->json($response);
    }

    /**
     * Validate FTP uploaded file (similar to existing validation but for file paths)
     */
    private function isValidFtpFile($filePath, $extension)
    {
        if (!in_array($extension, ['mp3', 'zip'])) {
            return false;
        }

        $content = file_get_contents($filePath, false, null, 0, 4096);
        
        if ($extension === 'mp3') {
            // Check for MP3 frame synchronization bytes
            return strpos($content, "\xFF\xFB") !== false || 
                   strpos($content, "\xFF\xFA") !== false ||
                   strpos($content, "\xFF\xF3") !== false ||
                   strpos($content, "\xFF\xF2") !== false;
        }
        
        if ($extension === 'zip') {
            // Check for ZIP file signature
            return strpos($content, "PK\x03\x04") === 0;
        }
        
        return false;
    }

    /**
     * Generate unique title handling naming conflicts
     */
    private function generateUniqueTitle($baseTitle)
    {
        $title = $baseTitle;
        $counter = 1;
        
        while (File::where('title', $title)->exists()) {
            $counter++;
            $title = $baseTitle . '-' . $counter;
        }
        
        return $title;
    }
}
