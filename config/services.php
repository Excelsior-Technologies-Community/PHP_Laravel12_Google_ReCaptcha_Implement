<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Google reCAPTCHA
    |--------------------------------------------------------------------------
    */

    'recaptcha' => [

        'key' => env('GOOGLE_RECAPTCHA_KEY'),

        'secret' => env('GOOGLE_RECAPTCHA_SECRET'),

        'v3_secret' => env('GOOGLE_RECAPTCHA_V3_SECRET'),

        'version' => env(
            'GOOGLE_RECAPTCHA_VERSION',
            'v2'
        ),

        'admin_email' => env(
            'ADMIN_EMAIL',
            'admin@example.com'
        ),

        'max_failures' => env(
            'MAX_RECAPTCHA_FAILURES',
            5
        ),

        'ban_duration' => env(
            'BAN_DURATION_MINUTES',
            30
        ),

    ],

];