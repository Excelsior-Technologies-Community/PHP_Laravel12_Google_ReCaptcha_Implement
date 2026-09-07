<?php

namespace App\Http\Controllers;

use App\Models\ContactSubmission;
use App\Models\SecurityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display security dashboard with stats and charts.
     */
    public function index()
    {
        $totalSubmissions = ContactSubmission::count();
        $verifiedSubmissions = ContactSubmission::where('recaptcha_verified', true)->count();

        $failedRecaptcha = SecurityLog::whereIn('event_type', ['recaptcha_failed', 'recaptcha_missing'])->count();
        $recaptchaErrors = SecurityLog::where('event_type', 'recaptcha_error')->count();

        $recentSubmissions = ContactSubmission::latest()->take(10)->get();
        $recentSecurityLogs = SecurityLog::latest()->take(10)->get();

        $todaySubmissions = ContactSubmission::whereDate('created_at', today())->count();
        $blockedIps = \App\Models\BlockedIp::where('banned_until', '>', now())->count();
        $totalLogs = SecurityLog::count();

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
                'totalLogs'
            )
        );
    }

    /**
     * Show paginated contact submissions with search and filters.
     */
    public function submissions(Request $request)
    {
        $query = ContactSubmission::query();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%");
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('recaptcha_status')) {
            $status = $request->input('recaptcha_status');
            if ($status === 'passed') {
                $query->where('recaptcha_verified', true);
            } elseif ($status === 'failed') {
                $query->where('recaptcha_verified', false);
            }
        }

        $submissions = $query->latest()->paginate(20)->appends($request->query());

        return view('admin.submissions.index', compact('submissions'));
    }

    /**
     * Show single submission.
     */
    public function show($id)
    {
        $submission = ContactSubmission::findOrFail($id);

        return view('security.submission', compact('submission'));
    }

    /**
     * Delete a submission.
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

        return redirect()->route('admin.submissions.index')->with('success', 'Submission deleted successfully.');
    }

    /**
     * Delete multiple submissions.
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['integer', 'exists:contact_submissions,id'],
        ]);

        ContactSubmission::whereIn('id', $request->input('ids'))->delete();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Selected submissions deleted successfully.',
            ]);
        }

        return redirect()->route('submissions.index')->with('success', 'Selected submissions deleted successfully.');
    }

    /**
     * Export filtered submissions to CSV.
     */
    public function export(Request $request)
    {
        $query = ContactSubmission::query();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%");
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('recaptcha_status')) {
            $status = $request->input('recaptcha_status');
            if ($status === 'passed') {
                $query->where('recaptcha_verified', true);
            } elseif ($status === 'failed') {
                $query->where('recaptcha_verified', false);
            }
        }

        $submissions = $query->latest()->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="submissions_' . now()->format('Y-m-d_H-i-s') . '.csv"',
        ];

        $callback = function () use ($submissions) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Name', 'Email', 'Phone', 'Subject', 'Message', 'IP Address', 'User Agent', 'reCAPTCHA Verified', 'reCAPTCHA Version', 'Language', 'Created At']);

            foreach ($submissions as $submission) {
                fputcsv($file, [
                    $submission->id,
                    $submission->name,
                    $submission->email,
                    $submission->phone,
                    $submission->subject,
                    $submission->message,
                    $submission->ip_address,
                    $submission->user_agent,
                    $submission->recaptcha_verified ? 'Yes' : 'No',
                    $submission->recaptcha_version ?? 'N/A',
                    $submission->language ?? 'en',
                    $submission->created_at,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export security logs to CSV.
     */
    public function exportLogs()
    {
        $logs = SecurityLog::latest()->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="security_logs_' . now()->format('Y-m-d_H-i-s') . '.csv"',
        ];

        $callback = function () use ($logs) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'IP Address', 'Email', 'Event Type', 'Description', 'User Agent', 'Created At']);

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

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Display paginated security logs with search.
     */
    public function logs(Request $request)
    {
        $query = SecurityLog::query();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('event_type', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $logs = $query->latest()->paginate(20)->appends($request->query());

        return view('security.logs', compact('logs'));
    }

    /**
     * Return JSON data for Chart.js.
     */
    public function chart(Request $request)
    {
        $type = $request->input('type', 'daily_submissions');

        if ($type === 'failed_recaptcha') {
            $days = SecurityLog::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('count(*) as count')
            )
                ->whereIn('event_type', ['recaptcha_failed', 'recaptcha_missing'])
                ->where('created_at', '>=', now()->subDays(30))
                ->groupBy('date')
                ->orderBy('date', 'asc')
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
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        return response()->json([
            'labels' => $days->pluck('date'),
            'data' => $days->pluck('count'),
            'label' => 'Daily Submissions',
            'borderColor' => '#3b82f6',
        ]);
    }

    /**
     * Get chart data for dashboard.
     */
    private function getChartData()
    {
        $dailySubmissions = ContactSubmission::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('count(*) as count')
        )
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        $failedRecaptchaCounts = SecurityLog::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('count(*) as count')
        )
            ->whereIn('event_type', ['recaptcha_failed', 'recaptcha_missing'])
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        return [
            'daily_submissions' => [
                'labels' => $dailySubmissions->pluck('date'),
                'data' => $dailySubmissions->pluck('count'),
            ],
            'failed_recaptcha' => [
                'labels' => $failedRecaptchaCounts->pluck('date'),
                'data' => $failedRecaptchaCounts->pluck('count'),
            ],
        ];
    }
}
