<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Rules\ReCaptcha;

class ContactController extends Controller
{
    /**
     * Display the contact form view.
     */
    public function index()
    {
        return view('contactForm');
    }

    /**
     * Handle form submission.
     * Validate all fields including reCAPTCHA.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'                  => 'required',
            'email'                 => 'required|email',
            'phone'                 => 'required|digits:10|numeric',
            'subject'               => 'required',
            'message'               => 'required',
            'g-recaptcha-response'  => ['required', new ReCaptcha],
        ]);

        $input = $request->all();

        /**
         * -------------------------------------------
         * Here you can save the data to the database.
         * -------------------------------------------
         */
        dd($input);

        return back()->with(['success' => 'Contact form submitted successfully']);
    }
}
