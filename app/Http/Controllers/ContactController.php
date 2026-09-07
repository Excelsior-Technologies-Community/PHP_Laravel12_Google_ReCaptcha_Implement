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
     * Display the contact form.
     */
    public function index()
    {
        return view('contactForm');
    }

    /**
     * Handle contact form submission.
     */
    public function store(Request $request)
    {
        $ip = $request->ip();

        $blockedIp = BlockedIp::where('ip_address', $ip)
            ->where('banned_until', '>', now())
            ->first();

        if ($blockedIp) {
            SecurityLog::create([
                'ip_address' => $ip,
                'email' => $request->input('email'),
                'event_type' => 'ip_blocked',
                'description' => 'Blocked IP attempted to submit the contact form.',
                'user_agent' => $request->userAgent(),
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Your IP has been temporarily blocked due to suspicious activity.',
                ], 403);
            }

            return back()->withErrors([
                'global' => 'Your IP has been temporarily blocked due to suspicious activity.',
            ]);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'digits:10'],
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:5000'],
            'g-recaptcha-response' => ['required'],
            'honeypot' => ['nullable', new Honeypot],
        ]);

        $recaptchaVersion = $request->input('recaptcha_version', config('services.recaptcha.version', 'v2'));

        if ($recaptchaVersion === 'v3') {
            $recaptchaRule = new \App\Rules\RecaptchaV3;
        } else {
            $recaptchaRule = new \App\Rules\ReCaptcha;
        }

        $recaptchaPasses = $recaptchaRule->passes('g-recaptcha-response', $request->input('g-recaptcha-response'));

        if (!$recaptchaPasses) {
            $blockedIp = BlockedIp::where('ip_address', $ip)->first();

            if ($blockedIp) {
                $blockedIp->failed_attempts = $blockedIp->failed_attempts + 1;
            } else {
                $blockedIp = new BlockedIp([
                    'ip_address' => $ip,
                    'failed_attempts' => 1,
                    'reason' => 'reCAPTCHA verification failed',
                ]);
            }

            $maxFailures = config('services.recaptcha.max_failures', 5);
            $banDuration = config('services.recaptcha.ban_duration', 30);

            if ($blockedIp->failed_attempts >= $maxFailures) {
                $blockedIp->banned_until = now()->addMinutes($banDuration);
            }

            $blockedIp->save();

            SecurityLog::create([
                'ip_address' => $ip,
                'email' => $request->input('email'),
                'event_type' => 'recaptcha_failed',
                'description' => 'reCAPTCHA verification failed. Failed attempts: ' . $blockedIp->failed_attempts,
                'user_agent' => $request->userAgent(),
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Google reCAPTCHA verification failed. Please try again.',
                ], 422);
            }

            return back()->withInput()->withErrors([
                'g-recaptcha-response' => 'Google reCAPTCHA verification failed. Please try again.',
            ]);
        }

        $attachmentPath = null;

        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('attachments', 'public');
        }

        $submission = ContactSubmission::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'subject' => $validated['subject'],
            'message' => $validated['message'],
            'ip_address' => $ip,
            'user_agent' => $request->userAgent(),
            'recaptcha_verified' => true,
            'recaptcha_version' => $recaptchaVersion,
            'attachment_path' => $attachmentPath,
            'language' => $request->input('language', 'en'),
        ]);

        try {
            Mail::to(config('services.recaptcha.admin_email', env('ADMIN_EMAIL', 'admin@example.com')))
                ->send(new ContactSubmissionMail($submission));
        } catch (\Throwable $e) {
            SecurityLog::create([
                'ip_address' => $ip,
                'email' => $validated['email'],
                'event_type' => 'email_error',
                'description' => 'Failed to send admin notification email: ' . $e->getMessage(),
                'user_agent' => $request->userAgent(),
            ]);
        }

        $this->clearDraft($request);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Contact form submitted successfully!',
            ]);
        }

        return redirect()
            ->route('contact.us')
            ->with('success', 'Contact form submitted successfully!');
    }

    /**
     * Save form data to session as draft with expiry timestamp.
     */
    public function saveDraft(Request $request)
    {
        $request->validate([
            'name' => ['nullable', 'string', 'max:100'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'subject' => ['nullable', 'string', 'max:255'],
            'message' => ['nullable', 'string', 'max:5000'],
        ]);

        $draft = [
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'phone' => $request->input('phone'),
            'subject' => $request->input('subject'),
            'message' => $request->input('message'),
            'saved_at' => now()->toDateTimeString(),
            'expires_at' => now()->addDays(7)->toDateTimeString(),
        ];

        Session::put('contact_form_draft', $draft);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Draft saved successfully.',
                'draft' => $draft,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Draft saved successfully.',
        ]);
    }

    /**
     * Return draft data from session.
     */
    public function loadDraft()
    {
        $draft = Session::get('contact_form_draft');

        if (!$draft || now()->greaterThan($draft['expires_at'] ?? now())) {
            $this->clearDraft(request());

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'draft' => null,
                ]);
            }

            return response()->json([
                'success' => true,
                'draft' => null,
            ]);
        }

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'draft' => $draft,
            ]);
        }

        return response()->json([
            'success' => true,
            'draft' => $draft,
        ]);
    }

    /**
     * Remove draft from session.
     */
    public function clearDraft(Request $request)
    {
        Session::forget('contact_form_draft');

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Draft cleared.',
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Draft cleared.',
        ]);
    }
}
