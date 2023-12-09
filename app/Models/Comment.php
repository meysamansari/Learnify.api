<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @method static create(array $array)
 * @method static latest(string $string)
 * @method static findOrFail($comment_id)
 */
class Comment extends Model
{
    protected $fillable = [
        'message',
        'reply',
        'course_id',
        'user_id',
        'rate'
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);

    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
