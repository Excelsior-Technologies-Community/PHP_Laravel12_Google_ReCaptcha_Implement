<?php

use App\Http\Controllers\ContactController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Contact Form
|--------------------------------------------------------------------------
*/

Route::get('contact-us', [ContactController::class, 'index'])
    ->name('contact.us');

Route::post('contact-us', [ContactController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('contact.us.store');


/*
|--------------------------------------------------------------------------
| Security Dashboard
|--------------------------------------------------------------------------
*/

Route::get('security-dashboard', [DashboardController::class, 'index'])
    ->name('security.dashboard');


/*
|--------------------------------------------------------------------------
| Contact Submission Details
|--------------------------------------------------------------------------
*/

Route::get(
    'security-dashboard/submission/{id}',
    [DashboardController::class, 'submission']
)->name('security.submission');


/*
|--------------------------------------------------------------------------
| Security Logs
|--------------------------------------------------------------------------
*/

Route::get(
    'security-dashboard/logs',
    [DashboardController::class, 'logs']
)->name('security.logs');