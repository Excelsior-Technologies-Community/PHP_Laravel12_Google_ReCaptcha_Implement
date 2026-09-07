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

        <div>
            <a
                 href="{{ route('admin.dashboard') }}"
                class="btn btn-primary"
            >
                Dashboard
            </a>

            <a
                href="{{ route('admin.logs.export') }}"
                class="btn btn-success ml-2"
            >
                Export to CSV
            </a>

        </div>

    </div>


    {{-- Filters --}}

    <div class="card shadow-sm mb-4">

        <div class="card-body">

            <form method="GET" action="{{ route('admin.logs.index') }}">

                <div class="form-row">

                    <div class="col-md-4 mb-3">

                        <input
                            type="text"
                            name="search"
                            class="form-control"
                            placeholder="Search by IP or email..."
                            value="{{ request('search') }}"
                        >

                    </div>

                    <div class="col-md-3 mb-3">

                        <select name="event_type" class="form-control">

                            <option value="">All Event Types</option>

                            <option value="recaptcha_failed" {{ request('event_type') === 'recaptcha_failed' ? 'selected' : '' }}>
                                reCAPTCHA Failed
                            </option>

                            <option value="recaptcha_missing" {{ request('event_type') === 'recaptcha_missing' ? 'selected' : '' }}>
                                reCAPTCHA Missing
                            </option>

                            <option value="recaptcha_error" {{ request('event_type') === 'recaptcha_error' ? 'selected' : '' }}>
                                reCAPTCHA Error
                            </option>

                            <option value="ip_blocked" {{ request('event_type') === 'ip_blocked' ? 'selected' : '' }}>
                                IP Blocked
                            </option>

                            <option value="email_error" {{ request('event_type') === 'email_error' ? 'selected' : '' }}>
                                Email Error
                            </option>

                        </select>

                    </div>

                    <div class="col-md-2 mb-3">

                        <button type="submit" class="btn btn-primary">
                            Filter
                        </button>

                    </div>

                    <div class="col-md-3 mb-3">

                        <a href="{{ route('admin.logs.index') }}" class="btn btn-secondary">
                            Reset
                        </a>

                    </div>

                </div>

            </form>

        </div>

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
