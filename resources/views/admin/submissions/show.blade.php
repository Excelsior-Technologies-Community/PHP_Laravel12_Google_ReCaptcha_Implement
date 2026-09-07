@extends('admin.dashboard')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Submission Details</h1>
    <a href="{{ route('admin.submissions.index') }}" class="btn btn-secondary">Back to List</a>
</div>

<div class="card">
    <div class="card-header">
        <h5>Contact Submission #{{ $submission->id }}</h5>
    </div>
    <div class="card-body">
        <table class="table table-bordered">
            <tr>
                <th width="200">Name</th>
                <td>{{ $submission->name }}</td>
            </tr>
            <tr>
                <th>Email</th>
                <td>{{ $submission->email }}</td>
            </tr>
            <tr>
                <th>Subject</th>
                <td>{{ $submission->subject }}</td>
            </tr>
            <tr>
                <th>Message</th>
                <td>{{ $submission->message }}</td>
            </tr>
            <tr>
                <th>IP Address</th>
                <td>{{ $submission->ip_address }}</td>
            </tr>
            <tr>
                <th>Recaptcha Status</th>
                <td>
                    <span class="badge badge-{{ $submission->recaptcha_verified ? 'success' : 'danger' }}">
                        {{ $submission->recaptcha_verified ? 'Passed' : 'Failed' }}
                    </span>
                </td>
            </tr>
            <tr>
                <th>Created At</th>
                <td>{{ $submission->created_at->format('Y-m-d H:i:s') }}</td>
            </tr>
            @if ($submission->attachment_path)
                <tr>
                    <th>Attachment</th>
                    <td>
                        <a href="{{ asset('storage/' . $submission->attachment_path) }}" target="_blank">Download Attachment</a>
                    </td>
                </tr>
            @endif
        </table>
    </div>
</div>
@endsection
