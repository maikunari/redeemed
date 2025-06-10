<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FtpProcessingLog extends Model
{
    protected $fillable = [
        'processed_at',
        'total_files',
        'files_processed',
        'files_invalid',
        'files_conflicts',
        'files_failed',
        'processing_details',
        'errors',
        'success',
        'processing_time_ms',
        'user_id'
    ];

    protected $casts = [
        'processed_at' => 'datetime',
        'processing_details' => 'array',
        'errors' => 'array',
        'success' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getFormattedProcessedAtAttribute(): string
    {
        return $this->processed_at->format('M j, Y g:i A');
    }

    public function getProcessingTimeSecAttribute(): ?string
    {
        if (!$this->processing_time_ms) return null;
        
        $seconds = round($this->processing_time_ms / 1000, 1);
        return $seconds . 's';
    }

    public function getSummaryAttribute(): string
    {
        $parts = [];
        
        if ($this->files_processed > 0) {
            $parts[] = "{$this->files_processed} processed";
        }
        
        if ($this->files_invalid > 0) {
            $parts[] = "{$this->files_invalid} invalid deleted";
        }
        
        if ($this->files_conflicts > 0) {
            $parts[] = "{$this->files_conflicts} conflicts resolved";
        }
        
        if ($this->files_failed > 0) {
            $parts[] = "{$this->files_failed} failed";
        }
        
        return implode(', ', $parts) ?: 'No files processed';
    }
}
