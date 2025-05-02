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
            // Generate a random code using numbers 2-9 (format: XXXXXX)
            $code = implode('', array_map(fn() => rand(2, 9), range(1, 6)));
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
            \Illuminate\Support\Facades\Log::debug('Validation failed for code: ' . $code . ' - Code not found');
            return null;
        }

        // Check if code has expired
        if ($downloadCode->expires_at && $downloadCode->expires_at->isPast()) {
            \Illuminate\Support\Facades\Log::debug('Validation failed for code: ' . $code . ' - Code expired at ' . $downloadCode->expires_at->toString());
            return null;
        }

        // Check if usage limit has been reached
        if ($downloadCode->usage_count >= $downloadCode->usage_limit) {
            \Illuminate\Support\Facades\Log::debug('Validation failed for code: ' . $code . ' - Usage limit reached: ' . $downloadCode->usage_count . '/' . $downloadCode->usage_limit);
            return null;
        }

        \Illuminate\Support\Facades\Log::debug('Validation passed for code: ' . $code . ' - Usage: ' . $downloadCode->usage_count . '/' . $downloadCode->usage_limit);
        return $downloadCode;
    }

    /**
     * Record a code usage
     */
    public function recordUsage(DownloadCode $downloadCode): void
    {
        // Log for debugging to see if this method is called multiple times
        $requestId = request()->header('X-Request-Id', uniqid());
        $requestPath = request()->path();
        $requestIp = request()->ip();
        \Illuminate\Support\Facades\Log::debug('Recording usage for code: ' . $downloadCode->code . ' at ' . now()->toString() . ' with Request ID: ' . $requestId . ', Path: ' . $requestPath . ', IP: ' . $requestIp);
        // Use a request-specific flag to prevent multiple increments in the same request
        $cacheKey = 'usage_recorded_' . $downloadCode->code . '_' . md5(serialize([request()->path(), request()->ip(), now()->timestamp]));
        if (!\Illuminate\Support\Facades\Cache::has($cacheKey)) {
            $downloadCode->increment('usage_count');
            \Illuminate\Support\Facades\Cache::put($cacheKey, true, now()->addSeconds(5));
            \Illuminate\Support\Facades\Log::debug('Usage incremented for code: ' . $downloadCode->code);
        } else {
            \Illuminate\Support\Facades\Log::debug('Usage increment skipped for code: ' . $downloadCode->code . ' due to duplicate call in request');
        }
    }
} 