<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Http;

class ReCaptcha implements Rule
{
    /**
     * Determine if the reCAPTCHA validation passes.
     * This method sends the user’s token to Google for verification.
     */
    public function passes($attribute, $value)
    {
        $response = Http::get("https://www.google.com/recaptcha/api/siteverify", [
            'secret'   => env('GOOGLE_RECAPTCHA_SECRET'),
            'response' => $value,
        ]);

        // Google returns {"success": true/false}
        return $response->json()["success"];
    }

    /**
     * Custom validation error message.
     */
    public function message()
    {
        return 'Google reCAPTCHA verification failed.';
    }
}
