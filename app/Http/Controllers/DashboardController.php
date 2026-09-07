<?php

namespace App\Http\Controllers;

use App\Models\BlockedIp;
use App\Models\ContactSubmission;
use App\Models\SecurityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display security dashboard.
     */
    public function index()
    {
        $totalSubmissions = ContactSubmission::count();

        $verifiedSubmissions = ContactSubmission::where(
            'recaptcha_verified',
            true
        )->count();

        $failedRecaptcha = SecurityLog::whereIn(
            'event_type',
            [
                'recaptcha_failed',
                'recaptcha_missing'
            ]
        )->count();

        $recaptchaErrors = SecurityLog::where(
            'event_type',
            'recaptcha_error'
        )->count();

        $recentSubmissions = ContactSubmission::oldest()
            ->take(5)
            ->get();

        $recentSecurityLogs = SecurityLog::oldest()
            ->take(5)
            ->get();

        $todaySubmissions = ContactSubmission::whereDate(
            'created_at',
            today()
        )->count();

        $blockedIps = BlockedIp::where(
            'banned_until',
            '>',
            now()
        )->count();

        $totalLogs = SecurityLog::count();

        // New statistics

        $newSubmissions = ContactSubmission::where(
            'status',
            'new'
        )->count();

        $readSubmissions = ContactSubmission::where(
            'status',
            'read'
        )->count();

        $repliedSubmissions = ContactSubmission::where(
            'status',
            'replied'
        )->count();

        $closedSubmissions = ContactSubmission::where(
            'status',
            'closed'
        )->count();

        $highPriority = ContactSubmission::where(
            'priority',
            'high'
        )->count();

        $unreadSubmissions = ContactSubmission::where(
            'is_read',
            false
        )->count();

        $chartData = $this->getChartData();

        return view(
            'security.dashboard',
            compact(
                'totalSubmissions',
                'verifiedSubmissions',
                'failedRecaptcha',
                'recaptchaErrors',
                'recentSubmissions',
                'recentSecurityLogs',
                'chartData',
                'todaySubmissions',
                'blockedIps',
                'totalLogs',

                'newSubmissions',
                'readSubmissions',
                'repliedSubmissions',
                'closedSubmissions',
                'highPriority',
                'unreadSubmissions'
            )
        );
    }

    /**
     * Display submissions.
     */
    public function submissions(Request $request)
    {
        $query = ContactSubmission::query();

        /*
        |--------------------------------------------------------------------------
        | Search
        |--------------------------------------------------------------------------
        */

        if ($search = $request->input('search')) {

            $query->where(function ($q) use ($search) {

                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('subject', 'like', "%{$search}%")
                    ->orWhere('ip_address', 'like', "%{$search}%");
            });
        }

        /*
        |--------------------------------------------------------------------------
        | Date filter
        |--------------------------------------------------------------------------
        */

        if ($request->filled('date_from')) {

            $query->whereDate(
                'created_at',
                '>=',
                $request->date_from
            );
        }

        if ($request->filled('date_to')) {

            $query->whereDate(
                'created_at',
                '<=',
                $request->date_to
            );
        }

        /*
        |--------------------------------------------------------------------------
        | reCAPTCHA filter
        |--------------------------------------------------------------------------
        */

        if ($request->filled('recaptcha_status')) {

            if ($request->recaptcha_status === 'passed') {

                $query->where(
                    'recaptcha_verified',
                    true
                );
            }

            if ($request->recaptcha_status === 'failed') {

                $query->where(
                    'recaptcha_verified',
                    false
                );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Status filter
        |--------------------------------------------------------------------------
        */

        if ($request->filled('status')) {

            $query->where(
                'status',
                $request->status
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Priority filter
        |--------------------------------------------------------------------------
        */

        if ($request->filled('priority')) {

            $query->where(
                'priority',
                $request->priority
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Read filter
        |--------------------------------------------------------------------------
        */

        if ($request->filled('read_status')) {

            if ($request->read_status === 'read') {

                $query->where(
                    'is_read',
                    true
                );
            }

            if ($request->read_status === 'unread') {

                $query->where(
                    'is_read',
                    false
                );
            }
        }

        $submissions = $query
            ->latest()
            ->paginate(20)
            ->appends($request->query());

        return view(
            'admin.submissions.index',
            compact('submissions')
        );
    }

    /**
     * Show single submission.
     */
    public function show($id)
    {
        $submission = ContactSubmission::findOrFail($id);

        // Automatically mark as read
        $submission->update([
            'is_read' => true,
        ]);

        return view(
            'security.submission',
            compact('submission')
        );
    }

    /**
     * Update status.
     */
    public function updateStatus(
        Request $request,
        $id
    ) {
        $request->validate([
            'status' => [
                'required',
                'in:new,read,replied,closed'
            ],
        ]);

        $submission = ContactSubmission::findOrFail($id);

        $submission->update([
            'status' => $request->status,
        ]);

        return back()->with(
            'success',
            'Submission status updated successfully.'
        );
    }

    /**
     * Update priority.
     */
    public function updatePriority(
        Request $request,
        $id
    ) {
        $request->validate([
            'priority' => [
                'required',
                'in:low,medium,high'
            ],
        ]);

        $submission = ContactSubmission::findOrFail($id);

        $submission->update([
            'priority' => $request->priority,
        ]);

        return back()->with(
            'success',
            'Submission priority updated successfully.'
        );
    }

    /**
     * Save admin note.
     */
    public function updateNote(
        Request $request,
        $id
    ) {
        $request->validate([
            'admin_note' => [
                'nullable',
                'string',
                'max:5000'
            ],
        ]);

        $submission = ContactSubmission::findOrFail($id);

        $submission->update([
            'admin_note' => $request->admin_note,
        ]);

        return back()->with(
            'success',
            'Admin note saved successfully.'
        );
    }

    /**
     * Toggle read/unread.
     */
    public function toggleRead($id)
    {
        $submission = ContactSubmission::findOrFail($id);

        $submission->update([
            'is_read' => !$submission->is_read,
        ]);

        return back()->with(
            'success',
            $submission->is_read
                ? 'Submission marked as read.'
                : 'Submission marked as unread.'
        );
    }

    /**
     * Bulk status update.
     */
    public function bulkStatusUpdate(
        Request $request
    ) {
        $request->validate([
            'ids' => [
                'required',
                'array'
            ],

            'ids.*' => [
                'integer',
                'exists:contact_submissions,id'
            ],

            'status' => [
                'required',
                'in:new,read,replied,closed'
            ],
        ]);

        ContactSubmission::whereIn(
            'id',
            $request->ids
        )->update([
            'status' => $request->status,
        ]);

        return back()->with(
            'success',
            'Selected submissions status updated successfully.'
        );
    }

    /**
     * Delete submission.
     */
    public function destroy($id)
    {
        $submission = ContactSubmission::findOrFail($id);

        $submission->delete();

        if (request()->expectsJson()) {

            return response()->json([
                'success' => true,
                'message' => 'Submission deleted successfully.',
            ]);
        }

        return redirect()
            ->route('admin.submissions.index')
            ->with(
                'success',
                'Submission deleted successfully.'
            );
    }

    /**
     * Bulk delete.
     */
    public function bulkDelete(
        Request $request
    ) {
        $request->validate([
            'ids' => [
                'required',
                'array'
            ],

            'ids.*' => [
                'integer',
                'exists:contact_submissions,id'
            ],
        ]);

        ContactSubmission::whereIn(
            'id',
            $request->ids
        )->delete();

        return redirect()
            ->route('admin.submissions.index')
            ->with(
                'success',
                'Selected submissions deleted successfully.'
            );
    }

    /**
     * Export submissions.
     */
    public function export(Request $request)
    {
        $query = ContactSubmission::query();

        if ($search = $request->input('search')) {

            $query->where(function ($q) use ($search) {

                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('subject', 'like', "%{$search}%")
                    ->orWhere('ip_address', 'like', "%{$search}%");
            });
        }

        if ($request->filled('date_from')) {

            $query->whereDate(
                'created_at',
                '>=',
                $request->date_from
            );
        }

        if ($request->filled('date_to')) {

            $query->whereDate(
                'created_at',
                '<=',
                $request->date_to
            );
        }

        if ($request->filled('status')) {

            $query->where(
                'status',
                $request->status
            );
        }

        if ($request->filled('priority')) {

            $query->where(
                'priority',
                $request->priority
            );
        }

        $submissions = $query
            ->latest()
            ->get();

        $headers = [
            'Content-Type' => 'text/csv',

            'Content-Disposition' =>
            'attachment; filename="submissions_' .
                now()->format('Y-m-d_H-i-s') .
                '.csv"',
        ];

        $callback = function () use ($submissions) {

            $file = fopen(
                'php://output',
                'w'
            );

            fputcsv($file, [
                'ID',
                'Name',
                'Email',
                'Phone',
                'Subject',
                'Message',
                'IP Address',
                'reCAPTCHA Verified',
                'reCAPTCHA Version',
                'Status',
                'Priority',
                'Admin Note',
                'Read',
                'Language',
                'Created At',
            ]);

            foreach ($submissions as $submission) {

                fputcsv($file, [

                    $submission->id,

                    $submission->name,

                    $submission->email,

                    $submission->phone,

                    $submission->subject,

                    $submission->message,

                    $submission->ip_address,

                    $submission->recaptcha_verified
                        ? 'Yes'
                        : 'No',

                    $submission->recaptcha_version ?? 'N/A',

                    $submission->status,

                    $submission->priority,

                    $submission->admin_note,

                    $submission->is_read
                        ? 'Yes'
                        : 'No',

                    $submission->language ?? 'en',

                    $submission->created_at,
                ]);
            }

            fclose($file);
        };

        return response()->stream(
            $callback,
            200,
            $headers
        );
    }

    /**
     * Export security logs.
     */
    public function exportLogs()
    {
        $logs = SecurityLog::latest()->get();

        $headers = [
            'Content-Type' => 'text/csv',

            'Content-Disposition' =>
            'attachment; filename="security_logs_' .
                now()->format('Y-m-d_H-i-s') .
                '.csv"',
        ];

        $callback = function () use ($logs) {

            $file = fopen(
                'php://output',
                'w'
            );

            fputcsv($file, [
                'ID',
                'IP Address',
                'Email',
                'Event Type',
                'Description',
                'User Agent',
                'Created At',
            ]);

            foreach ($logs as $log) {

                fputcsv($file, [
                    $log->id,
                    $log->ip_address,
                    $log->email,
                    $log->event_type,
                    $log->description,
                    $log->user_agent,
                    $log->created_at,
                ]);
            }

            fclose($file);
        };

        return response()->stream(
            $callback,
            200,
            $headers
        );
    }

    /**
     * Security logs.
     */
    public function logs(Request $request)
    {
        $query = SecurityLog::query();

        if ($search = $request->input('search')) {

            $query->where(function ($q) use ($search) {

                $q->where(
                    'event_type',
                    'like',
                    "%{$search}%"
                )
                    ->orWhere(
                        'ip_address',
                        'like',
                        "%{$search}%"
                    )
                    ->orWhere(
                        'email',
                        'like',
                        "%{$search}%"
                    )
                    ->orWhere(
                        'description',
                        'like',
                        "%{$search}%"
                    );
            });
        }

        $logs = $query
            ->latest()
            ->paginate(20)
            ->appends($request->query());

        return view(
            'security.logs',
            compact('logs')
        );
    }

    /**
     * Chart API.
     */
    public function chart(Request $request)
    {
        $type = $request->input(
            'type',
            'daily_submissions'
        );

        if ($type === 'failed_recaptcha') {

            $days = SecurityLog::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('count(*) as count')
            )
                ->whereIn(
                    'event_type',
                    [
                        'recaptcha_failed',
                        'recaptcha_missing'
                    ]
                )
                ->where(
                    'created_at',
                    '>=',
                    now()->subDays(30)
                )
                ->groupBy('date')
                ->orderBy('date')
                ->get();

            return response()->json([
                'labels' => $days->pluck('date'),
                'data' => $days->pluck('count'),
                'label' => 'Failed reCAPTCHA Attempts',
                'borderColor' => '#ef4444',
            ]);
        }

        $days = ContactSubmission::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('count(*) as count')
        )
            ->where(
                'created_at',
                '>=',
                now()->subDays(30)
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return response()->json([
            'labels' => $days->pluck('date'),
            'data' => $days->pluck('count'),
            'label' => 'Daily Submissions',
            'borderColor' => '#3b82f6',
        ]);
    }

    /**
     * Dashboard chart data.
     */
    private function getChartData()
    {
        $dailySubmissions = ContactSubmission::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('count(*) as count')
        )
            ->where(
                'created_at',
                '>=',
                now()->subDays(30)
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $failedRecaptchaCounts = SecurityLog::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('count(*) as count')
        )
            ->whereIn(
                'event_type',
                [
                    'recaptcha_failed',
                    'recaptcha_missing'
                ]
            )
            ->where(
                'created_at',
                '>=',
                now()->subDays(30)
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return [
            'daily_submissions' => [
                'labels' =>
                $dailySubmissions->pluck('date'),

                'data' =>
                $dailySubmissions->pluck('count'),
            ],

            'failed_recaptcha' => [
                'labels' =>
                $failedRecaptchaCounts->pluck('date'),

                'data' =>
                $failedRecaptchaCounts->pluck('count'),
            ],
        ];
    }
}
