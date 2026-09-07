@extends('admin.dashboard')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Security Logs</h1>
</div>

<div class="mb-3">
    <form method="GET" action="{{ route('admin.logs.index') }}" class="form-inline">
        <input type="text" name="search" class="form-control mr-2" placeholder="Search by IP or Email" value="{{ request('search') }}">
        <button type="submit" class="btn btn-primary mr-2">Search</button>
        <a href="{{ route('admin.logs.index') }}" class="btn btn-secondary">Reset</a>
    </form>
</div>

<div class="mb-3">
    <a href="{{ route('admin.logs.export') }}" class="btn btn-success">Export CSV</a>
</div>

<div class="table-responsive">
    <table class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>Event Type</th>
                <th>IP Address</th>
                <th>Email</th>
                <th>Description</th>
                <th>User Agent</th>
                <th>Time</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($logs as $log)
                <tr>
                    <td>{{ $log->event_type }}</td>
                    <td>{{ $log->ip_address }}</td>
                    <td>{{ $log->email }}</td>
                    <td>{{ $log->description }}</td>
                    <td>{{ Str::limit($log->user_agent, 50) }}</td>
                    <td>{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">No logs found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-3">
    {{ $logs->appends(request()->query())->links() }}
</div>
@endsection
