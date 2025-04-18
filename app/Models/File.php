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

    public function codes()
    {
        return $this->hasMany(DownloadCode::class);
    }
}
