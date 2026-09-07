<?php

namespace App\Rules;

use App\Models\SecurityLog;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Http;

class RecaptchaV3 implements Rule
{
    public function passes($attribute, $value)
    {
        try {
            $response = Http::timeout(10)->get(
                'https://www.google.com/recaptcha/api/siteverify',
                [
                    'secret'   => config('services.recaptcha.v3_secret', config('services.recaptcha.secret')),
                    'response' => $value,
                    'remoteip' => request()->ip(),
                ]
            );

            $result = $response->json();

            $score = $result['score'] ?? 0;

            if (
                $response->successful() &&
                isset($result['success']) &&
                $result['success'] === true &&
                $score >= 0.5
            ) {
                return true;
            }

            $errorCodes = $result['error-codes'] ?? [];

            SecurityLog::create([
                'ip_address' => request()->ip(),
                'email' => request()->input('email'),
                'event_type' => 'recaptcha_failed',
                'description' => !empty($errorCodes)
                    ? 'Google reCAPTCHA v3 verification failed: ' . implode(', ', $errorCodes)
                    : 'Google reCAPTCHA v3 score too low: ' . $score,
                'user_agent' => request()->userAgent(),
            ]);

            return false;
        } catch (\Throwable $e) {
            SecurityLog::create([
                'ip_address' => request()->ip(),
                'email' => request()->input('email'),
                'event_type' => 'recaptcha_error',
                'description' => 'Unable to communicate with Google reCAPTCHA service.',
                'user_agent' => request()->userAgent(),
            ]);

            return false;
        }
    }

    public function message()
    {
        return 'Google reCAPTCHA verification failed. Please try again.';
    }
}
