@extends('admin.dashboard')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Contact Submissions</h1>
</div>

<div class="mb-3">
    <form method="GET" action="{{ route('admin.submissions.index') }}" class="form-inline">
        <input type="text" name="search" class="form-control mr-2" placeholder="Search by name or email" value="{{ request('search') }}">

        <input type="date" name="date_from" class="form-control mr-2" value="{{ request('date_from') }}" title="From Date">
        <input type="date" name="date_to" class="form-control mr-2" value="{{ request('date_to') }}" title="To Date">

        <select name="recaptcha_status" class="form-control mr-2">
            <option value="">All Recaptcha Status</option>
            <option value="passed" {{ request('recaptcha_status') == 'passed' ? 'selected' : '' }}>Passed</option>
            <option value="failed" {{ request('recaptcha_status') == 'failed' ? 'selected' : '' }}>Failed</option>
        </select>

        <button type="submit" class="btn btn-primary mr-2">Filter</button>
        <a href="{{ route('admin.submissions.index') }}" class="btn btn-secondary">Reset</a>
    </form>
</div>

<div class="mb-3">
    <form method="POST" action="{{ route('admin.submissions.bulk-delete') }}" id="bulk-delete-form">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure?')">Bulk Delete</button>
        <a href="{{ route('admin.submissions.export') }}" class="btn btn-success">Export CSV</a>
    </form>
</div>

<div class="table-responsive">
    <table class="table table-striped table-bordered">
        <thead>
            <tr>
                <th><input type="checkbox" id="select-all"></th>
                <th>Name</th>
                <th>Email</th>
                <th>Subject</th>
                <th>IP</th>
                <th>Status</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($submissions as $submission)
                <tr>
                     <td><input type="checkbox" name="ids[]" value="{{ $submission->id }}" class="submission-checkbox"></td>
                    <td>{{ $submission->name }}</td>
                    <td>{{ $submission->email }}</td>
                    <td>{{ $submission->subject }}</td>
                    <td>{{ $submission->ip_address }}</td>
                    <td>
                        <span class="badge badge-{{ $submission->recaptcha_verified ? 'success' : 'danger' }}">
                            {{ $submission->recaptcha_verified ? 'Passed' : 'Failed' }}
                        </span>
                    </td>
                    <td>{{ $submission->created_at->format('Y-m-d H:i') }}</td>
                    <td>
                        <a href="{{ route('admin.submissions.show', $submission) }}" class="btn btn-sm btn-info">View</a>
                        <form action="{{ route('admin.submissions.destroy', $submission) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center">No submissions found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-3">
    {{ $submissions->appends(request()->query())->links() }}
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('select-all').addEventListener('change', function(e) {
        document.querySelectorAll('.submission-checkbox').forEach(function(cb) {
            cb.checked = e.target.checked;
        });
    });
</script>
@endpush
