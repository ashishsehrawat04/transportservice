<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserLoginOtp extends Model
{
    protected $fillable = [
        'mobile',
        'otp_hash',
        'expires_at',
        'verified_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'verified_at' => 'datetime',
    ];
}
