<?php

namespace App\Services;

use App\Models\DownloadCode;
use App\Models\File;
use Carbon\Carbon;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class DownloadCodeService
{
    /**
     * Generate a unique download code
     */
    private function generateUniqueCode(): string
    {
        do {
            // Generate a random code (format: XXXX-XXXX-XXXX)
            $code = strtoupper(implode('-', [
                Str::random(4),
                Str::random(4),
                Str::random(4)
            ]));
        } while (DownloadCode::where('code', $code)->exists());

        return $code;
    }

    /**
     * Create a new download code for a file
     */
    public function createCode(File $file, int $usageLimit, ?Carbon $expiresAt = null): DownloadCode
    {
        return DownloadCode::create([
            'file_id' => $file->id,
            'code' => $this->generateUniqueCode(),
            'usage_limit' => $usageLimit,
            'usage_count' => 0,
            'expires_at' => $expiresAt,
        ]);
    }

    /**
     * Generate a QR code for a download code
     */
    public function generateQrCode(DownloadCode $downloadCode): string
    {
        $url = route('codes.redeem-form', ['code' => $downloadCode->code]);
        return QrCode::size(300)
                    ->format('svg')
                    ->generate($url);
    }

    /**
     * Validate a download code
     */
    public function validateCode(string $code): ?DownloadCode
    {
        $downloadCode = DownloadCode::where('code', $code)->first();

        if (!$downloadCode) {
            return null;
        }

        // Check if code has expired
        if ($downloadCode->expires_at && $downloadCode->expires_at->isPast()) {
            return null;
        }

        // Check if usage limit has been reached
        if ($downloadCode->usage_count >= $downloadCode->usage_limit) {
            return null;
        }

        return $downloadCode;
    }

    /**
     * Record a code usage
     */
    public function recordUsage(DownloadCode $downloadCode): void
    {
        $downloadCode->increment('usage_count');
    }
} 