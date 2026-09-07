<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'subject',
        'message',
        'ip_address',
        'user_agent',
        'recaptcha_verified',
        'recaptcha_version',
        'attachment_path',
        'language',
    ];

    protected $casts = [
        'recaptcha_verified' => 'boolean',
    ];
}