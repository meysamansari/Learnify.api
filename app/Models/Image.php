<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Image extends Model implements HasMedia
{
    use HasFactory,InteractsWithMedia;
    protected $fillable = [
        'status',
        'imageable_id',
        'imageable_type'
    ];
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('image');
    }

    public function imageable(): MorphTo
    {
        return $this->morphTo();
    }
}
