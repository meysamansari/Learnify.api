<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Verification extends Model
{
    use HasFactory;
    protected $fillable = [
        'phone_number',
        'verification_code',
        'verification_valid_until',
    ];
    protected $hidden = [
        'verification_code',
    ];
}
