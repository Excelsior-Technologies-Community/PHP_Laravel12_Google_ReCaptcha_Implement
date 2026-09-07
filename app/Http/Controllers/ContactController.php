<?php

namespace App\Http\Controllers;

use App\Mail\ContactSubmissionMail;
use App\Models\BlockedIp;
use App\Models\ContactSubmission;
use App\Models\SecurityLog;
use App\Rules\Honeypot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;

class ContactController extends Controller
{
    /**
     * Display contact form.
     */
    public function index()
    {
        return view('contactForm');
    }

    /**
     * Handle contact form.
     */
    public function store(Request $request)
    {
        $ip = $request->ip();

        /*
        |--------------------------------------------------------------------------
        | Feature 7 - Blocked IP Check
        |--------------------------------------------------------------------------
        */

        $blockedIp = BlockedIp::where(
            'ip_address',
            $ip
        )
            ->where(
                'banned_until',
                '>',
                now()
            )
            ->first();

        if ($blockedIp) {

            SecurityLog::create([
                'ip_address' => $ip,

                'email' =>
                $request->input('email'),

                'event_type' =>
                'ip_blocked',

                'description' =>
                'Blocked IP attempted to submit the contact form.',

                'user_agent' =>
                $request->userAgent(),
            ]);

            return $this->errorResponse(
                $request,
                'Your IP has been temporarily blocked due to suspicious activity.',
                403
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Validation
        |--------------------------------------------------------------------------
        */

        $validated = $request->validate([

            'name' => [
                'required',
                'string',
                'max:100'
            ],

            'email' => [
                'required',
                'email',
                'max:255'
            ],

            'phone' => [
                'required',
                'digits:10'
            ],

            'subject' => [
                'required',
                'string',
                'max:255'
            ],

            'message' => [
                'required',
                'string',
                'max:5000'
            ],

            'g-recaptcha-response' => [
                'required'
            ],

            'honeypot' => [
                'nullable',
                new Honeypot
            ],

            /*
            |--------------------------------------------------------------------------
            | Feature 6 - Attachment Validation
            |--------------------------------------------------------------------------
            */

            'attachment' => [
                'nullable',
                'file',
                'mimes:pdf,doc,docx,jpg,jpeg,png',
                'max:5120'
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | reCAPTCHA
        |--------------------------------------------------------------------------
        */

        $recaptchaVersion = $request->input(
            'recaptcha_version',
            config(
                'services.recaptcha.version',
                'v2'
            )
        );

        if ($recaptchaVersion === 'v3') {

            $recaptchaRule =
                new \App\Rules\RecaptchaV3;
        } else {

            $recaptchaRule =
                new \App\Rules\ReCaptcha;
        }

        $recaptchaPasses =
            $recaptchaRule->passes(
                'g-recaptcha-response',
                $request->input(
                    'g-recaptcha-response'
                )
            );

        if (!$recaptchaPasses) {

            $blockedIp = BlockedIp::where(
                'ip_address',
                $ip
            )->first();

            if ($blockedIp) {

                $blockedIp->failed_attempts =
                    $blockedIp->failed_attempts + 1;
            } else {

                $blockedIp = new BlockedIp([
                    'ip_address' =>
                    $ip,

                    'failed_attempts' =>
                    1,

                    'reason' =>
                    'reCAPTCHA verification failed',
                ]);
            }

            $maxFailures = config(
                'services.recaptcha.max_failures',
                5
            );

            $banDuration = config(
                'services.recaptcha.ban_duration',
                30
            );

            if (
                $blockedIp->failed_attempts >=
                $maxFailures
            ) {

                $blockedIp->banned_until =
                    now()->addMinutes(
                        $banDuration
                    );
            }

            $blockedIp->save();

            SecurityLog::create([
                'ip_address' =>
                $ip,

                'email' =>
                $request->input('email'),

                'event_type' =>
                'recaptcha_failed',

                'description' =>
                'reCAPTCHA verification failed. Failed attempts: ' .
                    $blockedIp->failed_attempts,

                'user_agent' =>
                $request->userAgent(),
            ]);

            return $this->errorResponse(
                $request,
                'Google reCAPTCHA verification failed. Please try again.',
                422,
                [
                    'g-recaptcha-response' => [
                        'Google reCAPTCHA verification failed. Please try again.'
                    ]
                ]
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Feature 7 - Duplicate Submission Prevention
        |--------------------------------------------------------------------------
        */

        $duplicate = ContactSubmission::where(
            'email',
            $validated['email']
        )
            ->where(
                'message',
                $validated['message']
            )
            ->where(
                'created_at',
                '>=',
                now()->subMinutes(10)
            )
            ->first();

        if ($duplicate) {

            SecurityLog::create([
                'ip_address' =>
                $ip,

                'email' =>
                $validated['email'],

                'event_type' =>
                'duplicate_submission',

                'description' =>
                'Duplicate contact form submission prevented.',

                'user_agent' =>
                $request->userAgent(),
            ]);

            return $this->errorResponse(
                $request,
                'Duplicate submission detected. Please wait before submitting the same message again.',
                422
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Attachment
        |--------------------------------------------------------------------------
        */

        $attachmentPath = null;

        if ($request->hasFile('attachment')) {

            $attachmentPath =
                $request->file('attachment')
                ->store(
                    'attachments',
                    'public'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | Create submission
        |--------------------------------------------------------------------------
        */

        $submission = ContactSubmission::create([

            'name' =>
            $validated['name'],

            'email' =>
            $validated['email'],

            'phone' =>
            $validated['phone'],

            'subject' =>
            $validated['subject'],

            'message' =>
            $validated['message'],

            'ip_address' =>
            $ip,

            'user_agent' =>
            $request->userAgent(),

            'recaptcha_verified' =>
            true,

            'recaptcha_version' =>
            $recaptchaVersion,

            'attachment_path' =>
            $attachmentPath,

            'language' =>
            $request->input(
                'language',
                'en'
            ),

            /*
            |--------------------------------------------------------------------------
            | New fields
            |--------------------------------------------------------------------------
            */

            'status' =>
            'new',

            'priority' =>
            'medium',

            'is_read' =>
            false,

            'admin_note' =>
            null,
        ]);

        /*
        |--------------------------------------------------------------------------
        | Send email
        |--------------------------------------------------------------------------
        */

        try {

            Mail::to(
                config(
                    'services.recaptcha.admin_email',
                    env(
                        'ADMIN_EMAIL',
                        'admin@example.com'
                    )
                )
            )->send(
                new ContactSubmissionMail(
                    $submission
                )
            );
        } catch (\Throwable $e) {

            SecurityLog::create([
                'ip_address' =>
                $ip,

                'email' =>
                $validated['email'],

                'event_type' =>
                'email_error',

                'description' =>
                'Failed to send admin notification email: ' .
                    $e->getMessage(),

                'user_agent' =>
                $request->userAgent(),
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Clear draft
        |--------------------------------------------------------------------------
        */

        Session::forget(
            'contact_form_draft'
        );

        return $this->successResponse(
            $request,
            'Contact form submitted successfully!'
        );
    }

    /**
     * Save draft.
     */
    public function saveDraft(Request $request)
    {
        $request->validate([

            'name' => [
                'nullable',
                'string',
                'max:100'
            ],

            'email' => [
                'nullable',
                'email',
                'max:255'
            ],

            'phone' => [
                'nullable',
                'string',
                'max:20'
            ],

            'subject' => [
                'nullable',
                'string',
                'max:255'
            ],

            'message' => [
                'nullable',
                'string',
                'max:5000'
            ],
        ]);

        $draft = [

            'name' =>
            $request->input('name'),

            'email' =>
            $request->input('email'),

            'phone' =>
            $request->input('phone'),

            'subject' =>
            $request->input('subject'),

            'message' =>
            $request->input('message'),

            'saved_at' =>
            now()->toDateTimeString(),

            'expires_at' =>
            now()
                ->addDays(7)
                ->toDateTimeString(),
        ];

        Session::put(
            'contact_form_draft',
            $draft
        );

        return response()->json([
            'success' => true,
            'message' =>
            'Draft saved successfully.',
            'draft' =>
            $draft,
        ]);
    }

    /**
     * Load draft.
     */
    public function loadDraft()
    {
        $draft = Session::get(
            'contact_form_draft'
        );

        if (
            !$draft ||
            now()->greaterThan(
                $draft['expires_at'] ?? now()
            )
        ) {

            Session::forget(
                'contact_form_draft'
            );

            return response()->json([
                'success' => true,
                'draft' => null,
            ]);
        }

        return response()->json([
            'success' => true,
            'draft' => $draft,
        ]);
    }

    /**
     * Clear draft.
     */
    public function clearDraft(
        Request $request
    ) {
        Session::forget(
            'contact_form_draft'
        );

        return response()->json([
            'success' => true,
            'message' =>
            'Draft cleared.',
        ]);
    }

    /**
     * Success response.
     */
    private function successResponse(
        Request $request,
        string $message
    ) {
        if ($request->expectsJson()) {

            return response()->json([
                'success' => true,
                'message' => $message,
            ]);
        }

        return redirect()
            ->route('contact.us')
            ->with(
                'success',
                $message
            );
    }

    /**
     * Error response.
     */
    private function errorResponse(
        Request $request,
        string $message,
        int $status = 422,
        array $errors = []
    ) {
        if ($request->expectsJson()) {

            return response()->json([
                'success' => false,
                'message' => $message,
                'errors' => $errors,
            ], $status);
        }

        return back()
            ->withInput()
            ->withErrors([
                'global' => $message
            ]);
    }
}
