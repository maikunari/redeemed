<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Image\Enums\Fit;

class Settings extends Model implements HasMedia
{
    use InteractsWithMedia;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'site_name',
    ];

    protected $appends = ['logo_url'];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('logos')
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/gif']);
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('logo')
            ->performOnCollections('logos')
            ->width(150)
            ->height(150)
            ->fit(Fit::Contain)
            ->background('ffffff')
            ->quality(100)
            ->nonQueued();
    }

    public function getLogoUrlAttribute()
    {
        $media = $this->getFirstMedia('logos');
        return $media ? $media->getUrl('logo') : null;
    }
} 