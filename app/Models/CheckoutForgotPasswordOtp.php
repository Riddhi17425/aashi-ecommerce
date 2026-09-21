<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CheckoutForgotPasswordOtp extends Model
{
    protected $fillable = [
        'email',
        'otp',
        'is_verified',
        'expires_at',
    ];

    protected $casts = [
        'is_verified' => 'boolean',
        'expires_at'  => 'datetime',
    ];
}