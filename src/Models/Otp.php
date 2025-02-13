<?php

namespace PutheaKhem\Otp\Models;

use Illuminate\Database\Eloquent\Model;

class Otp extends Model
{
    protected $fillable = ['identifier', 'otp', 'used', 'attempts', 'session_id', 'expires_at'];

    protected $casts = [
        'used' => 'boolean',
        'expires_at' => 'datetime',
    ];
}
