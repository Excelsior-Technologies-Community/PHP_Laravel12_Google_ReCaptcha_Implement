<?php

use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Authentication
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->middleware('web')->group(function () {
    Route::get('login', [AdminAuthController::class, 'showLoginForm'])->middleware('guest.admin')->name('admin.login');
    Route::post('login', [AdminAuthController::class, 'login'])->middleware('guest.admin')->name('admin.login.post');
    Route::post('logout', [AdminAuthController::class, 'logout'])->name('admin.logout');
    Route::get('dashboard', [DashboardController::class, 'index'])->middleware('admin')->name('admin.dashboard');
});

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

Route::post('contact-us/draft', [ContactController::class, 'saveDraft'])
    ->name('contact.us.draft');

Route::get('contact-us/draft', [ContactController::class, 'loadDraft'])
    ->name('contact.us.draft.load');

Route::delete('contact-us/draft', [ContactController::class, 'clearDraft'])
    ->name('contact.us.draft.clear');


/*
|--------------------------------------------------------------------------
| Security Dashboard (Admin)
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->middleware(['web', 'admin'])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])
        ->name('admin.dashboard');

    Route::get('submissions', [DashboardController::class, 'submissions'])
        ->name('admin.submissions.index');

    Route::get('submissions/export', [DashboardController::class, 'export'])
        ->name('admin.submissions.export');

    Route::post('submissions/bulk-delete', [DashboardController::class, 'bulkDelete'])
        ->name('admin.submissions.bulk-delete');

    Route::get('submissions/{id}', [DashboardController::class, 'show'])
        ->name('admin.submissions.show');

    Route::delete('submissions/{id}', [DashboardController::class, 'destroy'])
        ->name('admin.submissions.destroy');

    Route::get('logs', [DashboardController::class, 'logs'])
        ->name('admin.logs.index');

    Route::get('logs/export', [DashboardController::class, 'exportLogs'])
        ->name('admin.logs.export');

    Route::get('chart', [DashboardController::class, 'chart'])
        ->name('dashboard.chart');
});

/*
|--------------------------------------------------------------------------
| Security Dashboard
|--------------------------------------------------------------------------
*/

Route::prefix('admin')
    ->middleware(['web', 'admin'])
    ->group(function () {

        Route::get(
            'dashboard',
            [DashboardController::class, 'index']
        )->name('admin.dashboard');

        /*
        |--------------------------------------------------------------------------
        | Submissions
        |--------------------------------------------------------------------------
        */

        Route::get(
            'submissions',
            [DashboardController::class, 'submissions']
        )->name('admin.submissions.index');

        Route::get(
            'submissions/export',
            [DashboardController::class, 'export']
        )->name('admin.submissions.export');

        /*
        |--------------------------------------------------------------------------
        | Bulk Actions
        |--------------------------------------------------------------------------
        */

        Route::post(
            'submissions/bulk-delete',
            [DashboardController::class, 'bulkDelete']
        )->name('admin.submissions.bulk-delete');

        Route::post(
            'submissions/bulk-status',
            [DashboardController::class, 'bulkStatusUpdate']
        )->name('admin.submissions.bulk-status');

        /*
        |--------------------------------------------------------------------------
        | Single Submission
        |--------------------------------------------------------------------------
        */

        Route::get(
            'submissions/{id}',
            [DashboardController::class, 'show']
        )->name('admin.submissions.show');

        Route::delete(
            'submissions/{id}',
            [DashboardController::class, 'destroy']
        )->name('admin.submissions.destroy');

        /*
        |--------------------------------------------------------------------------
        | Status
        |--------------------------------------------------------------------------
        */

        Route::patch(
            'submissions/{id}/status',
            [DashboardController::class, 'updateStatus']
        )->name('admin.submissions.status');

        /*
        |--------------------------------------------------------------------------
        | Priority
        |--------------------------------------------------------------------------
        */

        Route::patch(
            'submissions/{id}/priority',
            [DashboardController::class, 'updatePriority']
        )->name('admin.submissions.priority');

        /*
        |--------------------------------------------------------------------------
        | Admin Note
        |--------------------------------------------------------------------------
        */

        Route::patch(
            'submissions/{id}/note',
            [DashboardController::class, 'updateNote']
        )->name('admin.submissions.note');

        /*
        |--------------------------------------------------------------------------
        | Read / Unread
        |--------------------------------------------------------------------------
        */

        Route::patch(
            'submissions/{id}/toggle-read',
            [DashboardController::class, 'toggleRead']
        )->name('admin.submissions.toggle-read');

        /*
        |--------------------------------------------------------------------------
        | Logs
        |--------------------------------------------------------------------------
        */

        Route::get(
            'logs',
            [DashboardController::class, 'logs']
        )->name('admin.logs.index');

        Route::get(
            'logs/export',
            [DashboardController::class, 'exportLogs']
        )->name('admin.logs.export');

        /*
        |--------------------------------------------------------------------------
        | Charts
        |--------------------------------------------------------------------------
        */

        Route::get(
            'chart',
            [DashboardController::class, 'chart']
        )->name('dashboard.chart');
    });