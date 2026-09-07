<?php

namespace App\Http\Controllers;

use App\Models\ContactSubmission;
use App\Models\SecurityLog;
use App\Rules\ReCaptcha;
use Illuminate\Http\Request;

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
        /*
        |--------------------------------------------------------------------------
        | Check if reCAPTCHA was completed
        |--------------------------------------------------------------------------
        |
        | Laravel's "required" validation rule stops the custom ReCaptcha
        | rule from running when the checkbox is not selected.
        |
        | Therefore, we manually log missing reCAPTCHA attempts here.
        |
        */

        if (!$request->filled('g-recaptcha-response')) {

            SecurityLog::create([
                'ip_address' => $request->ip(),

                'email' => $request->input('email'),

                'event_type' => 'recaptcha_missing',

                'description' =>
                    'Contact form submitted without completing Google reCAPTCHA.',

                'user_agent' => $request->userAgent(),
            ]);

            return back()
                ->withInput()
                ->withErrors([
                    'g-recaptcha-response' =>
                        'Please complete the Google reCAPTCHA verification.',
                ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Validate Form
        |--------------------------------------------------------------------------
        */

        $validated = $request->validate([

            'name' => [
                'required',
                'string',
                'max:100',
            ],

            'email' => [
                'required',
                'email',
                'max:255',
            ],

            'phone' => [
                'required',
                'digits:10',
            ],

            'subject' => [
                'required',
                'string',
                'max:255',
            ],

            'message' => [
                'required',
                'string',
                'max:5000',
            ],

            'g-recaptcha-response' => [
                'required',
                new ReCaptcha,
            ],

        ]);

        /*
        |--------------------------------------------------------------------------
        | Save Successful Contact Submission
        |--------------------------------------------------------------------------
        */

        ContactSubmission::create([

            'name' => $validated['name'],

            'email' => $validated['email'],

            'phone' => $validated['phone'],

            'subject' => $validated['subject'],

            'message' => $validated['message'],

            'ip_address' => $request->ip(),

            'user_agent' => $request->userAgent(),

            'recaptcha_verified' => true,

        ]);

        /*
        |--------------------------------------------------------------------------
        | Redirect After Successful Submission
        |--------------------------------------------------------------------------
        */

        return redirect()
            ->route('contact.us')
            ->with(
                'success',
                'Contact form submitted successfully!'
            );
    }
}