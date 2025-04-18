<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class File extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = [
        'title',
        'path',
        'thumbnail',
    ];

    protected $appends = ['file_size', 'file_type', 'formatted_created_at'];

    public function codes()
    {
        return $this->hasMany(DownloadCode::class);
    }

    public function getFileSizeAttribute()
    {
        $media = $this->getFirstMedia('files');
        if (!$media) return null;

        $bytes = $media->size;
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    public function getFileTypeAttribute()
    {
        $media = $this->getFirstMedia('files');
        if (!$media) return null;

        $extension = strtolower($media->extension);
        return $extension === 'mp3' ? 'audio' : 'archive';
    }

    public function getFormattedCreatedAtAttribute()
    {
        return $this->created_at->format('M j, Y g:i A');
    }
}
