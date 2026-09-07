<?php

namespace App\Rules;

use App\Models\SecurityLog;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class Honeypot implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!empty($value)) {
            SecurityLog::create([
                'ip_address' => request()->ip(),
                'email' => request()->input('email'),
                'event_type' => 'honeypot_detected',
                'description' => 'Honeypot field was filled.',
                'user_agent' => request()->userAgent(),
            ]);

            $fail('Spam detected.');
        }
    }
}
