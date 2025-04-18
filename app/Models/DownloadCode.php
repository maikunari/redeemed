<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DownloadCode extends Model
{
    protected $fillable = [
        'code',
        'file_id',
        'usage_limit',
        'usage_count',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    public function file()
    {
        return $this->belongsTo(File::class);
    }
}
