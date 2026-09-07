<!DOCTYPE html>
<html lang="en">

<head>

    <title>Security Dashboard</title>

    <meta charset="utf-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1">

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/css/bootstrap.min.css">

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js">
    </script>

    <style>
        body {
            background: #f5f7fa;
        }

        .stat-card {
            border: none;
            border-radius: 12px;
        }

        .stat-number {
            font-size: 32px;
            font-weight: bold;
        }

        .table-card {
            border: none;
            border-radius: 12px;
        }

        .chart-container {
            position: relative;
            height: 300px;
        }
    </style>

</head>

<body>

    <div class="container py-5">

        <div class="d-flex justify-content-between align-items-center mb-4">

            <div>

                <h2>
                    Security Dashboard
                </h2>

                <p class="text-muted mb-0">
                    Contact form and reCAPTCHA security monitoring
                </p>

            </div>

            <div>

                <a
                    href="{{ route('contact.us') }}"
                    class="btn btn-primary">
                    Contact Form
                </a>

                <a
                    href="{{ route('admin.logs.index') }}"
                    class="btn btn-dark">
                    Security Logs
                </a>

            </div>

        </div>


        {{-- Statistics --}}
        <div class="row">

            <div class="col-md-3 mb-4">

                <div class="card shadow-sm stat-card">

                    <div class="card-body">

                        <h6 class="text-muted">
                            Total Submissions
                        </h6>

                        <div class="stat-number">
                            {{ $totalSubmissions }}
                        </div>

                    </div>

                </div>

            </div>

            <div class="col-md-3 mb-4">

                <div class="card shadow-sm stat-card">

                    <div class="card-body">

                        <h6 class="text-muted">
                            Today's Submissions
                        </h6>

                        <div class="stat-number text-info">
                            {{ $todaySubmissions ?? 0 }}
                        </div>

                    </div>

                </div>

            </div>

            <div class="col-md-3 mb-4">

                <div class="card shadow-sm stat-card">

                    <div class="card-body">

                        <h6 class="text-muted">
                            Verified
                        </h6>

                        <div class="stat-number text-success">
                            {{ $verifiedSubmissions }}
                        </div>

                    </div>

                </div>

            </div>

            <div class="col-md-3 mb-4">

                <div class="card shadow-sm stat-card">

                    <div class="card-body">

                        <h6 class="text-muted">
                            Blocked IPs
                        </h6>

                        <div class="stat-number text-danger">
                            {{ $blockedIps ?? 0 }}
                        </div>

                    </div>

                </div>

            </div>

            <div class="col-md-3 mb-4">

                <div class="card shadow-sm stat-card">

                    <div class="card-body">

                        <h6 class="text-muted">
                            Total Logs
                        </h6>

                        <div class="stat-number text-warning">
                            {{ $totalLogs ?? 0 }}
                        </div>

                    </div>

                </div>

            </div>

            <div class="col-md-3 mb-4">

                <div class="card shadow-sm stat-card">

                    <div class="card-body">

                        <h6 class="text-muted">
                            Failed reCAPTCHA
                        </h6>

                        <div class="stat-number text-danger">
                            {{ $failedRecaptcha }}
                        </div>

                    </div>

                </div>

            </div>

            <div class="col-md-3 mb-4">

                <div class="card shadow-sm stat-card">

                    <div class="card-body">

                        <h6 class="text-muted">
                            reCAPTCHA Errors
                        </h6>

                        <div class="stat-number text-warning">
                            {{ $recaptchaErrors }}
                        </div>

                    </div>

                </div>

            </div>

        </div>


        {{-- Charts --}}
        <div class="row mb-4">

            <div class="col-md-6 mb-4">

                <div class="card shadow-sm table-card">

                    <div class="card-header bg-primary text-white">

                        <h5 class="mb-0">
                            Daily Submissions
                        </h5>

                    </div>

                    <div class="card-body">

                        <div class="chart-container">
                            <canvas id="dailySubmissionsChart"></canvas>
                        </div>

                    </div>

                </div>

            </div>

            <div class="col-md-6 mb-4">

                <div class="card shadow-sm table-card">

                    <div class="card-header bg-danger text-white">

                        <h5 class="mb-0">
                            Failed reCAPTCHA
                        </h5>

                    </div>

                    <div class="card-body">

                        <div class="chart-container">
                            <canvas id="failedRecaptchaChart"></canvas>
                        </div>

                    </div>

                </div>

            </div>

        </div>


        {{-- Recent Submissions --}}
        <div class="card shadow-sm table-card mb-4">

            <div class="card-header bg-primary text-white">

                <h5 class="mb-0">
                    Recent Contact Submissions
                </h5>

            </div>

            <div class="card-body">

                @if($recentSubmissions->count())

                <div class="table-responsive">

                    <table class="table table-bordered table-hover">

                        <thead>

                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Subject</th>
                                <th>IP Address</th>
                                <th>Status</th>
                                <th>Time</th>
                                <th>Action</th>
                            </tr>

                        </thead>

                        <tbody>

                            @foreach($recentSubmissions as $submission)

                            <tr>

                                <td>
                                    {{ $submission->name }}
                                </td>

                                <td>
                                    {{ $submission->email }}
                                </td>

                                <td>
                                    {{ $submission->subject }}
                                </td>

                                <td>
                                    {{ $submission->ip_address }}
                                </td>

                                <td>

                                    @if($submission->recaptcha_verified)

                                    <span class="badge badge-success">
                                        Verified
                                    </span>

                                    @else

                                    <span class="badge badge-danger">
                                        Failed
                                    </span>

                                    @endif

                                </td>

                                <td>
                                    {{ $submission->created_at->format('d M Y H:i') }}
                                </td>

                                <td>

                                    <a
                                        href="{{ route('admin.submissions.show', $submission->id) }}"
                                        class="btn btn-sm btn-primary">
                                        View
                                    </a>

                                </td>

                            </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>

                @else

                <p class="text-muted mb-0">
                    No contact submissions yet.
                </p>

                @endif

            </div>

        </div>


        {{-- Recent Security Logs --}}
        <div class="card shadow-sm table-card">

            <div class="card-header bg-dark text-white">

                <h5 class="mb-0">
                    Recent Security Events
                </h5>

            </div>

            <div class="card-body">

                @if($recentSecurityLogs->count())

                <div class="table-responsive">

                    <table class="table table-bordered">

                        <thead>

                            <tr>
                                <th>Event</th>
                                <th>IP Address</th>
                                <th>Email</th>
                                <th>Description</th>
                                <th>Time</th>
                            </tr>

                        </thead>

                        <tbody>

                            @foreach($recentSecurityLogs as $log)

                            <tr>

                                <td>

                                    @if($log->event_type === 'recaptcha_failed')

                                    <span class="badge badge-danger">
                                        reCAPTCHA Failed
                                    </span>

                                    @elseif($log->event_type === 'recaptcha_missing')

                                    <span class="badge badge-warning">
                                        reCAPTCHA Missing
                                    </span>

                                    @elseif($log->event_type === 'recaptcha_error')

                                    <span class="badge badge-dark">
                                        reCAPTCHA Error
                                    </span>

                                    @else

                                    <span class="badge badge-secondary">
                                        {{ $log->event_type }}
                                    </span>

                                    @endif

                                </td>

                                <td>
                                    {{ $log->ip_address }}
                                </td>

                                <td>
                                    {{ $log->email ?? '-' }}
                                </td>

                                <td>
                                    {{ $log->description }}
                                </td>

                                <td>
                                    {{ $log->created_at->format('d M Y H:i') }}
                                </td>

                            </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>

                @else

                <p class="text-muted mb-0">
                    No security events recorded.
                </p>

                @endif

            </div>

        </div>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const chartUrl = '{{ route('dashboard.chart') }}';

            function initChart(canvasId, type, label, borderColor, dataKey) {
                const ctx = document.getElementById(canvasId);
                if (!ctx) return;

                fetch(chartUrl + '?type=' + dataKey)
                    .then(function (response) { return response.json(); })
                    .then(function (chartData) {
                        new Chart(ctx, {
                            type: type,
                            data: {
                                labels: chartData.labels,
                                datasets: [{
                                    label: label,
                                    data: chartData.data,
                                    borderColor: borderColor,
                                    backgroundColor: type === 'bar' ? borderColor : borderColor + '33',
                                    fill: type === 'line',
                                    tension: 0.3,
                                    pointRadius: 3,
                                    pointHoverRadius: 5
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                scales: {
                                    y: {
                                        beginAtZero: true,
                                        ticks: {
                                            stepSize: 1
                                        }
                                    },
                                    x: {
                                        ticks: {
                                            maxTicksLimit: 10
                                        }
                                    }
                                },
                                plugins: {
                                    legend: {
                                        display: true,
                                        position: 'top'
                                    }
                                }
                            }
                        });
                    })
                    .catch(function (error) {
                        console.error('Failed to load chart data:', error);
                    });
            }

            initChart('dailySubmissionsChart', 'line', 'Daily Submissions', '#3b82f6', 'daily_submissions');
            initChart('failedRecaptchaChart', 'bar', 'Failed reCAPTCHA', '#ef4444', 'failed_recaptcha');
        });
    </script>

</body>

</html>
