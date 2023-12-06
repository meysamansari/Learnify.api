<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Resume extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'description',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
