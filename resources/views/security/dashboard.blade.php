<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <meta
        name="csrf-token"
        content="{{ csrf_token() }}">

    <title>Security Dashboard</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        body {
            background: #f5f7fa;
        }

        .dashboard-card {
            border: 0;
            border-radius: 12px;
            transition: 0.2s;
        }

        .dashboard-card:hover {
            transform: translateY(-3px);
        }

        .stat-number {
            font-size: 30px;
            font-weight: 700;
        }

        .table-card {
            border: 0;
            border-radius: 12px;
        }

        .badge {
            font-size: 12px;
        }

        .chart-container {
            position: relative;
            height: 350px;
        }

        .pagination {
            margin-bottom: 0;
        }
    </style>

</head>

<body>

    <div class="container-fluid py-4">

        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4">

            <div>

                <h2 class="mb-1">
                    🔐 Security Dashboard
                </h2>

                <p class="text-muted mb-0">
                    Laravel Google reCAPTCHA Contact Form Security Monitor
                </p>

            </div>

            <div>

                <a
                    href="{{ route('contact.us') }}"
                    class="btn btn-primary">
                    Contact Form
                </a>

                <form
                    action="{{ route('admin.logout') }}"
                    method="POST"
                    class="d-inline">

                    @csrf

                    <button
                        type="submit"
                        class="btn btn-outline-danger">
                        Logout
                    </button>

                </form>

            </div>

        </div>


        {{-- Statistics --}}
        <div class="row g-4 mb-4">

            {{-- Total --}}
            <div class="col-xl-2 col-md-4 col-sm-6">

                <div class="card shadow-sm dashboard-card">

                    <div class="card-body">

                        <div class="text-muted">
                            Total Submissions
                        </div>

                        <div class="stat-number text-primary">
                            {{ $totalSubmissions }}
                        </div>

                    </div>

                </div>

            </div>


            {{-- Today --}}
            <div class="col-xl-2 col-md-4 col-sm-6">

                <div class="card shadow-sm dashboard-card">

                    <div class="card-body">

                        <div class="text-muted">
                            Today
                        </div>

                        <div class="stat-number text-success">
                            {{ $todaySubmissions }}
                        </div>

                    </div>

                </div>

            </div>


            {{-- Verified --}}
            <div class="col-xl-2 col-md-4 col-sm-6">

                <div class="card shadow-sm dashboard-card">

                    <div class="card-body">

                        <div class="text-muted">
                            Verified
                        </div>

                        <div class="stat-number text-info">
                            {{ $verifiedSubmissions }}
                        </div>

                    </div>

                </div>

            </div>


            {{-- Failed --}}
            <div class="col-xl-2 col-md-4 col-sm-6">

                <div class="card shadow-sm dashboard-card">

                    <div class="card-body">

                        <div class="text-muted">
                            Failed reCAPTCHA
                        </div>

                        <div class="stat-number text-danger">
                            {{ $failedRecaptcha }}
                        </div>

                    </div>

                </div>

            </div>


            {{-- Blocked --}}
            <div class="col-xl-2 col-md-4 col-sm-6">

                <div class="card shadow-sm dashboard-card">

                    <div class="card-body">

                        <div class="text-muted">
                            Blocked IPs
                        </div>

                        <div class="stat-number text-warning">
                            {{ $blockedIps }}
                        </div>

                    </div>

                </div>

            </div>


            {{-- Logs --}}
            <div class="col-xl-2 col-md-4 col-sm-6">

                <div class="card shadow-sm dashboard-card">

                    <div class="card-body">

                        <div class="text-muted">
                            Security Logs
                        </div>

                        <div class="stat-number text-secondary">
                            {{ $totalLogs }}
                        </div>

                    </div>

                </div>

            </div>

        </div>


        {{-- Quick Actions --}}
        <div class="card shadow-sm table-card mb-4">

            <div class="card-body">

                <div class="d-flex flex-wrap gap-2">

                    <a
                        href="{{ route('admin.submissions.index') }}"
                        class="btn btn-primary">
                        📋 View Submissions
                    </a>

                    <a
                        href="{{ route('admin.submissions.export') }}"
                        class="btn btn-success">
                        📥 Export Submissions CSV
                    </a>

                    <a
                        href="{{ route('admin.logs.index') }}"
                        class="btn btn-warning">
                        🛡️ Security Logs
                    </a>

                    <a
                        href="{{ route('admin.logs.export') }}"
                        class="btn btn-dark">
                        📥 Export Logs CSV
                    </a>

                </div>

            </div>

        </div>


        {{-- Chart --}}
        <div class="card shadow-sm table-card mb-4">

            <div class="card-header bg-white">

                <div class="d-flex justify-content-between align-items-center">

                    <h5 class="mb-0">
                        📊 Submission Analytics
                    </h5>

                    <select
                        id="chart-type"
                        class="form-select"
                        style="width:220px;">

                        <option value="daily_submissions">
                            Daily Submissions
                        </option>

                        <option value="failed_recaptcha">
                            Failed reCAPTCHA
                        </option>

                    </select>

                </div>

            </div>

            <div class="card-body">

                <div class="chart-container">

                    <canvas id="securityChart"></canvas>

                </div>

            </div>

        </div>


        {{-- Recent Submissions --}}
        <div class="card shadow-sm table-card mb-4">

            <div class="card-header bg-white">

                <div class="d-flex justify-content-between align-items-center">

                    <h5 class="mb-0">
                        📩 Recent Submissions
                    </h5>

                    <a
                        href="{{ route('admin.submissions.index') }}"
                        class="btn btn-sm btn-primary">
                        View All
                    </a>

                </div>

            </div>

            <div class="card-body p-0">

                <div class="table-responsive">

                    <table class="table table-hover mb-0">

                        <thead class="table-light">

                            <tr>

                                <th>ID</th>

                                <th>Name</th>

                                <th>Email</th>

                                <th>Phone</th>

                                <th>Subject</th>

                                <th>reCAPTCHA</th>

                                <th>Date</th>

                                <th>Action</th>

                            </tr>

                        </thead>

                        <tbody>

                            @forelse($recentSubmissions as $submission)

                            <tr>

                                <td>
                                    <strong>
                                        {{ $submission->id }}
                                    </strong>
                                </td>

                                <td>
                                    {{ $submission->name }}
                                </td>

                                <td>
                                    {{ $submission->email }}
                                </td>

                                <td>
                                    {{ $submission->phone }}
                                </td>

                                <td>
                                    {{ \Illuminate\Support\Str::limit($submission->subject, 30) }}
                                </td>

                                <td>

                                    @if($submission->recaptcha_verified)

                                    <span class="badge bg-success">
                                        Passed
                                    </span>

                                    @else

                                    <span class="badge bg-danger">
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
                                        class="btn btn-sm btn-outline-primary">
                                        View
                                    </a>

                                    <form
                                        action="{{ route('admin.submissions.destroy', $submission->id) }}"
                                        method="POST"
                                        class="d-inline delete-form">

                                        @csrf

                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="btn btn-sm btn-outline-danger">
                                            Delete
                                        </button>

                                    </form>

                                </td>

                            </tr>

                            @empty

                            <tr>

                                <td
                                    colspan="8"
                                    class="text-center py-4 text-muted">
                                    No submissions found.
                                </td>

                            </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

            </div>

        </div>


        {{-- Recent Security Logs --}}
        <div class="card shadow-sm table-card mb-4">

            <div class="card-header bg-white">

                <div class="d-flex justify-content-between align-items-center">

                    <h5 class="mb-0">
                        🛡️ Recent Security Logs
                    </h5>

                    <a
                        href="{{ route('admin.logs.index') }}"
                        class="btn btn-sm btn-warning">
                        View All Logs
                    </a>

                </div>

            </div>

            <div class="card-body p-0">

                <div class="table-responsive">

                    <table class="table table-hover mb-0">

                        <thead class="table-light">

                            <tr>

                                <th>ID</th>

                                <th>IP Address</th>

                                <th>Email</th>

                                <th>Event</th>

                                <th>Description</th>

                                <th>Date</th>

                            </tr>

                        </thead>

                        <tbody>

                            @forelse($recentSecurityLogs as $log)

                            <tr>

                                <td>
                                    {{ $log->id }}
                                </td>

                                <td>
                                    {{ $log->ip_address }}
                                </td>

                                <td>
                                    {{ $log->email ?? '-' }}
                                </td>

                                <td>

                                    @if($log->event_type === 'recaptcha_failed')

                                    <span class="badge bg-danger">
                                        reCAPTCHA Failed
                                    </span>

                                    @elseif($log->event_type === 'ip_blocked')

                                    <span class="badge bg-warning text-dark">
                                        IP Blocked
                                    </span>

                                    @elseif($log->event_type === 'email_error')

                                    <span class="badge bg-dark">
                                        Email Error
                                    </span>

                                    @else

                                    <span class="badge bg-secondary">
                                        {{ $log->event_type }}
                                    </span>

                                    @endif

                                </td>

                                <td>
                                    {{ \Illuminate\Support\Str::limit($log->description, 70) }}
                                </td>

                                <td>
                                    {{ $log->created_at->format('d M Y H:i') }}
                                </td>

                            </tr>

                            @empty

                            <tr>

                                <td
                                    colspan="6"
                                    class="text-center py-4 text-muted">
                                    No security logs found.
                                </td>

                            </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

            </div>

        </div>


        {{-- Dashboard Footer --}}
        <div class="text-center text-muted py-3">

            Laravel 12 · Google reCAPTCHA Security System

        </div>

    </div>


    <script>
        let securityChart = null;

        /*
        |--------------------------------------------------------------------------
        | Load Chart
        |--------------------------------------------------------------------------
        */

        function loadChart(type = 'daily_submissions') {

            fetch(
                    "{{ route('dashboard.chart') }}?type=" +
                    encodeURIComponent(type)
                )

                .then(response => response.json())

                .then(data => {

                    const ctx =
                        document.getElementById(
                            'securityChart'
                        ).getContext('2d');

                    if (securityChart) {

                        securityChart.destroy();

                    }

                    securityChart =
                        new Chart(
                            ctx, {
                                type: 'line',

                                data: {

                                    labels: data.labels,

                                    datasets: [

                                        {
                                            label: data.label,

                                            data: data.data,

                                            borderWidth: 2,

                                            tension: 0.3,

                                            fill: false
                                        }

                                    ]

                                },

                                options: {

                                    responsive: true,

                                    maintainAspectRatio: false,

                                    plugins: {

                                        legend: {
                                            display: true
                                        }

                                    },

                                    scales: {

                                        y: {

                                            beginAtZero: true,

                                            ticks: {

                                                precision: 0

                                            }

                                        }

                                    }

                                }

                            }
                        );

                })

                .catch(
                    error => console.error(
                        'Chart loading error:',
                        error
                    )
                );

        }


        /*
        |--------------------------------------------------------------------------
        | Initial Chart
        |--------------------------------------------------------------------------
        */

        document.addEventListener(
            'DOMContentLoaded',
            function() {

                loadChart(
                    'daily_submissions'
                );

                /*
                |--------------------------------------------------------------------------
                | Chart Filter
                |--------------------------------------------------------------------------
                */

                document
                    .getElementById('chart-type')
                    .addEventListener(
                        'change',
                        function() {

                            loadChart(
                                this.value
                            );

                        }
                    );


                /*
                |--------------------------------------------------------------------------
                | Delete Confirmation
                |--------------------------------------------------------------------------
                */

                document
                    .querySelectorAll('.delete-form')
                    .forEach(
                        function(form) {

                            form.addEventListener(
                                'submit',
                                function(event) {

                                    if (
                                        !confirm(
                                            'Are you sure you want to delete this submission?'
                                        )
                                    ) {

                                        event.preventDefault();

                                    }

                                }
                            );

                        }
                    );

            }
        );
    </script>

</body>

</html>
