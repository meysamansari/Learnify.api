<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'image_id',
        'teaser_id',
        'category',
        'price',
        'status',
        'step',
    ];
    public function users(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function chapters(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Chapter::class);
    }
}
