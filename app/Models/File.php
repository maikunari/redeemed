<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Image\Enums\Fit;

class File extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = ['title'];

    protected $appends = ['file_size', 'file_type', 'formatted_created_at', 'download_url', 'thumbnail_url'];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('files')
            ->singleFile()
            ->acceptsMimeTypes([
                'audio/mpeg',           // Standard MP3 MIME type
                'audio/mp3',            // Alternative MP3 MIME type
                'audio/mpeg3',          // Another MP3 variant
                'audio/x-mpeg-3',       // Legacy MP3 MIME type
                'application/octet-stream', // Generic binary file type (fallback for MP3s)
                'application/zip',       // ZIP files
                'application/x-zip',     // Alternative ZIP MIME type
                'application/x-zip-compressed' // Another ZIP variant
            ]);

        $this->addMediaCollection('thumbnails')
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/gif']);
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->performOnCollections('thumbnails')
            ->width(150)
            ->height(150)
            ->fit(Fit::Contain)
            ->background('ffffff')  // White background for transparent PNGs
            ->quality(100)         // Maximum quality
            ->nonQueued();
    }

    public function getDownloadUrlAttribute()
    {
        $media = $this->getFirstMedia('files');
        return $media ? $media->getUrl() : null;
    }

    public function getThumbnailUrlAttribute()
    {
        $media = $this->getFirstMedia('thumbnails');
        return $media ? $media->getUrl('thumb') : null;
    }

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
        return $this->created_at ? $this->created_at->format('M j, Y g:i A') : null;
    }
}
