<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use App\Models\File;

class ScanFtpStaging extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'files:scan-ftp-staging {--json : Return results as JSON}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scan FTP staging directory for uploaded files and return file information';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $stagingPath = storage_path('app/ftp-staging');
        
        if (!is_dir($stagingPath)) {
            $result = ['error' => 'FTP staging directory does not exist'];
            return $this->outputResult($result);
        }

        // Get all files in staging directory
        $files = [];
        $fileList = glob($stagingPath . '/*');
        
        foreach ($fileList as $filePath) {
            if (is_file($filePath)) {
                $fileInfo = $this->analyzeFile($filePath);
                if ($fileInfo) {
                    $files[] = $fileInfo;
                }
            }
        }

        $result = [
            'success' => true,
            'count' => count($files),
            'files' => $files
        ];

        return $this->outputResult($result);
    }

    /**
     * Analyze a file and return its information
     */
    private function analyzeFile($filePath)
    {
        $filename = basename($filePath);
        $filesize = filesize($filePath);
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        // Check if file type is valid (MP3 or ZIP)
        $isValid = $this->isValidFileType($filePath, $extension);
        
        // Generate proposed title (filename without extension)
        $proposedTitle = pathinfo($filename, PATHINFO_FILENAME);
        
        // Check for naming conflicts
        $finalTitle = $this->resolveNamingConflict($proposedTitle);
        
        return [
            'filename' => $filename,
            'filepath' => $filePath,
            'size' => $filesize,
            'size_formatted' => $this->formatFileSize($filesize),
            'extension' => $extension,
            'is_valid' => $isValid,
            'proposed_title' => $proposedTitle,
            'final_title' => $finalTitle,
            'modified_at' => filemtime($filePath),
            'error' => $isValid ? null : 'Invalid file type. Only MP3 and ZIP files are supported.'
        ];
    }

    /**
     * Validate file type (similar to FileController logic)
     */
    private function isValidFileType($filePath, $extension)
    {
        if (!in_array($extension, ['mp3', 'zip'])) {
            return false;
        }

        $content = file_get_contents($filePath, false, null, 0, 4096); // Read first 4KB
        
        if ($extension === 'mp3') {
            // Check for MP3 frame synchronization bytes
            return strpos($content, "\xFF\xFB") !== false || 
                   strpos($content, "\xFF\xFA") !== false ||
                   strpos($content, "\xFF\xF3") !== false ||
                   strpos($content, "\xFF\xF2") !== false;
        }
        
        if ($extension === 'zip') {
            // Check for ZIP file signature (PK\x03\x04)
            return strpos($content, "PK\x03\x04") === 0;
        }
        
        return false;
    }

    /**
     * Resolve naming conflicts by appending numbers
     */
    private function resolveNamingConflict($title)
    {
        $originalTitle = $title;
        $counter = 1;
        
        while (File::where('title', $title)->exists()) {
            $counter++;
            $title = $originalTitle . '-' . $counter;
        }
        
        return $title;
    }

    /**
     * Format file size in human readable format
     */
    private function formatFileSize($bytes)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Output result based on format option
     */
    private function outputResult($result)
    {
        if ($this->option('json')) {
            $this->line(json_encode($result));
            return $result['success'] ?? false ? 0 : 1;
        }

        if (isset($result['error'])) {
            $this->error($result['error']);
            return 1;
        }

        $this->info('Found ' . $result['count'] . ' files in FTP staging directory');
        
        if ($result['count'] > 0) {
            $this->table(
                ['Filename', 'Size', 'Type', 'Valid', 'Proposed Title'],
                collect($result['files'])->map(function ($file) {
                    return [
                        $file['filename'],
                        $file['size_formatted'],
                        strtoupper($file['extension']),
                        $file['is_valid'] ? '✓' : '✗',
                        $file['final_title']
                    ];
                })->toArray()
            );
        }

        return 0;
    }
}
