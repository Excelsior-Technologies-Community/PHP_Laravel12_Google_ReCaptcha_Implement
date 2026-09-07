<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRecaptchaVersion
{
    public function handle(Request $request, Closure $next): Response
    {
        $version = config('services.recaptcha.version', 'v2');

        if (!in_array($version, ['v2', 'v3'])) {
            abort(500, 'Unsupported reCAPTCHA version configured.');
        }

        return $next($request);
    }
}
