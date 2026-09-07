<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Http;

class RecaptchaV3 implements ValidationRule
{
    public function validate(
        string $attribute,
        mixed $value,
        \Closure $fail
    ): void {

        if (!$value) {
            $fail('reCAPTCHA verification is required.');
            return;
        }

        $response = Http::asForm()->post(
            'https://www.google.com/recaptcha/api/siteverify',
            [
                'secret' => config('services.recaptcha.secret'),
                'response' => $value,
                'remoteip' => request()->ip(),
            ]
        );

        if (!$response->successful()) {
            $fail('Unable to verify reCAPTCHA.');
            return;
        }

        $data = $response->json();

        if (!($data['success'] ?? false)) {
            $fail('Google reCAPTCHA verification failed.');
            return;
        }

        $score = (float) ($data['score'] ?? 0);

        $expectedAction = config(
            'services.recaptcha.action',
            'submit'
        );

        if (
            isset($data['action']) &&
            $data['action'] !== $expectedAction
        ) {
            $fail('Invalid reCAPTCHA action.');
            return;
        }

        $minimumScore = (float) config(
            'services.recaptcha.score_threshold',
            0.5
        );

        if ($score < $minimumScore) {
            $fail('reCAPTCHA score is too low.');
            return;
        }
    }
}
