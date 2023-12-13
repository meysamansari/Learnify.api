<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Video extends Model implements HasMedia
{
    use HasFactory,InteractsWithMedia;
    protected $fillable = [
        'status',
        'videoable_id',
        'videoable_type'
        ];
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('video');
    }
    public function videoable(): MorphTo
    {
        return $this->morphTo();
    }
}
