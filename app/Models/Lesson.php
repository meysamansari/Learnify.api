<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Lesson extends Model
{
    use HasFactory;
    protected $fillable = [
        'chapter_id',
        'title',
        'time',
        'visibility',
        'video_id',
    ];
    public function chapters(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Chapter::class);
    }
    public function video(): MorphOne
    {
        return $this->morphOne(Video::class, 'videoable');
    }
}
