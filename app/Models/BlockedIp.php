<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlockedIp extends Model
{
    use HasFactory;

    protected $fillable = [
        'ip_address',
        'banned_until',
        'failed_attempts',
        'reason',
    ];

    protected $casts = [
        'banned_until' => 'datetime',
    ];
}
