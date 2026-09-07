<!DOCTYPE html>
<html lang="en">

<head>

    <title>Security Logs</title>

    <meta charset="utf-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/css/bootstrap.min.css"
    >

</head>

<body>

<div class="container py-5">

    {{-- Header --}}

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h2>
                Security Logs
            </h2>

            <p class="text-muted mb-0">
                Failed reCAPTCHA and security events
            </p>

        </div>

        <a
            href="{{ route('security.dashboard') }}"
            class="btn btn-primary"
        >
            Dashboard
        </a>

    </div>


    {{-- Logs Table --}}

    <div class="card shadow-sm">

        <div class="card-body">

            @if($logs->count())

                <div class="table-responsive">

                    <table class="table table-bordered table-hover">

                        <thead class="thead-dark">

                            <tr>

                                <th>ID</th>

                                <th>Event</th>

                                <th>IP Address</th>

                                <th>Email</th>

                                <th>Description</th>

                                <th>User Agent</th>

                                <th>Time</th>

                            </tr>

                        </thead>


                        <tbody>

                        @foreach($logs as $log)

                            <tr>

                                {{-- ID --}}

                                <td>
                                    {{ $log->id }}
                                </td>


                                {{-- Event --}}

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


                                {{-- IP Address --}}

                                <td>
                                    {{ $log->ip_address ?? '-' }}
                                </td>


                                {{-- Email --}}

                                <td>
                                    {{ $log->email ?? '-' }}
                                </td>


                                {{-- Description --}}

                                <td>
                                    {{ $log->description ?? '-' }}
                                </td>


                                {{-- User Agent --}}

                                <td>

                                    <small>
                                        {{ $log->user_agent ?? '-' }}
                                    </small>

                                </td>


                                {{-- Time --}}

                                <td>

                                    {{ $log->created_at->format('d M Y H:i:s') }}

                                </td>

                            </tr>

                        @endforeach

                        </tbody>

                    </table>

                </div>


                {{-- Pagination --}}

                <div class="mt-3">

                    {{ $logs->links() }}

                </div>

            @else

                <div class="alert alert-success">

                    No security issues have been recorded.

                </div>

            @endif

        </div>

    </div>

</div>

</body>

</html>