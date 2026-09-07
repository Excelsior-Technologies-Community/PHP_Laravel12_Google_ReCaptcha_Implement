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
                    href="{{ route('security.logs') }}"
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
                                        href="{{ route('security.submission', $submission->id) }}"
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

</body>

</html>