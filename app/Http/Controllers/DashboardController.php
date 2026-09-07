<?php

namespace App\Http\Controllers;

use App\Models\ContactSubmission;
use App\Models\SecurityLog;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display security dashboard.
     */
    public function index()
    {
        /*
        |--------------------------------------------------------------------------
        | Contact Submission Statistics
        |--------------------------------------------------------------------------
        */

        $totalSubmissions = ContactSubmission::count();

        $verifiedSubmissions = ContactSubmission::where(
            'recaptcha_verified',
            true
        )->count();


        /*
        |--------------------------------------------------------------------------
        | Failed reCAPTCHA Statistics
        |--------------------------------------------------------------------------
        |
        | Count both:
        |
        | recaptcha_failed
        | recaptcha_missing
        |
        */

        $failedRecaptcha = SecurityLog::whereIn(
            'event_type',
            [
                'recaptcha_failed',
                'recaptcha_missing',
            ]
        )->count();


        /*
        |--------------------------------------------------------------------------
        | reCAPTCHA Communication Errors
        |--------------------------------------------------------------------------
        */

        $recaptchaErrors = SecurityLog::where(
            'event_type',
            'recaptcha_error'
        )->count();


        /*
        |--------------------------------------------------------------------------
        | Recent Contact Submissions
        |--------------------------------------------------------------------------
        */

        $recentSubmissions = ContactSubmission::latest()
            ->take(10)
            ->get();


        /*
        |--------------------------------------------------------------------------
        | Recent Security Logs
        |--------------------------------------------------------------------------
        */

        $recentSecurityLogs = SecurityLog::latest()
            ->take(10)
            ->get();


        /*
        |--------------------------------------------------------------------------
        | Dashboard View
        |--------------------------------------------------------------------------
        */

        return view(
            'security.dashboard',
            compact(
                'totalSubmissions',
                'verifiedSubmissions',
                'failedRecaptcha',
                'recaptchaErrors',
                'recentSubmissions',
                'recentSecurityLogs'
            )
        );
    }


    /**
     * Show individual contact submission.
     */
    public function submission($id)
    {
        $submission = ContactSubmission::findOrFail($id);

        return view(
            'security.submission',
            compact('submission')
        );
    }


    /**
     * Display security logs.
     */
    public function logs()
    {
        $logs = SecurityLog::latest()
            ->paginate(15);

        return view(
            'security.logs',
            compact('logs')
        );
    }
}