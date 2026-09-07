<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Contact Submissions</title>

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">

    <style>
        body {
            background: #f5f7fa;
        }

        .card {
            border: none;
            border-radius: 12px;
        }

        .status-new {
            background: #ffc107;
            color: #000;
        }

        .status-read {
            background: #17a2b8;
            color: #fff;
        }

        .status-replied {
            background: #28a745;
            color: #fff;
        }

        .status-closed {
            background: #6c757d;
            color: #fff;
        }

        .priority-low {
            color: #28a745;
            font-weight: bold;
        }

        .priority-medium {
            color: #ffc107;
            font-weight: bold;
        }

        .priority-high {
            color: #dc3545;
            font-weight: bold;
        }

        .unread-row {
            background: #fffbea;
            font-weight: 600;
        }
    </style>

</head>

<body>

    <div class="container-fluid py-4">

        <div class="d-flex justify-content-between mb-4">

            <h3>
                Contact Submissions
            </h3>

            <div>

                <a
                    href="{{ route('admin.dashboard') }}"
                    class="btn btn-primary">
                    Dashboard
                </a>

                <a
                    href="{{ route('admin.submissions.export', request()->query()) }}"
                    class="btn btn-success">
                    Export CSV
                </a>

            </div>

        </div>

        @if(session('success'))

        <div class="alert alert-success">
            {{ session('success') }}
        </div>

        @endif

        {{-- Search & Filters --}}

        <div class="card shadow mb-4">

            <div class="card-body">

                <form method="GET">

                    <div class="row">

                        <div class="col-md-3 mb-2">

                            <input
                                type="text"
                                name="search"
                                class="form-control"
                                placeholder="Search name, email, phone..."
                                value="{{ request('search') }}">

                        </div>

                        <div class="col-md-2 mb-2">

                            <select
                                name="status"
                                class="form-control">

                                <option value="">
                                    All Status
                                </option>

                                <option
                                    value="new"
                                    {{ request('status') === 'new' ? 'selected' : '' }}>
                                    New
                                </option>

                                <option
                                    value="read"
                                    {{ request('status') === 'read' ? 'selected' : '' }}>
                                    Read
                                </option>

                                <option
                                    value="replied"
                                    {{ request('status') === 'replied' ? 'selected' : '' }}>
                                    Replied
                                </option>

                                <option
                                    value="closed"
                                    {{ request('status') === 'closed' ? 'selected' : '' }}>
                                    Closed
                                </option>

                            </select>

                        </div>

                        <div class="col-md-2 mb-2">

                            <select
                                name="priority"
                                class="form-control">

                                <option value="">
                                    All Priority
                                </option>

                                <option
                                    value="low"
                                    {{ request('priority') === 'low' ? 'selected' : '' }}>
                                    Low
                                </option>

                                <option
                                    value="medium"
                                    {{ request('priority') === 'medium' ? 'selected' : '' }}>
                                    Medium
                                </option>

                                <option
                                    value="high"
                                    {{ request('priority') === 'high' ? 'selected' : '' }}>
                                    High
                                </option>

                            </select>

                        </div>

                        <div class="col-md-2 mb-2">

                            <select
                                name="read_status"
                                class="form-control">

                                <option value="">
                                    Read Status
                                </option>

                                <option
                                    value="unread"
                                    {{ request('read_status') === 'unread' ? 'selected' : '' }}>
                                    Unread
                                </option>

                                <option
                                    value="read"
                                    {{ request('read_status') === 'read' ? 'selected' : '' }}>
                                    Read
                                </option>

                            </select>

                        </div>

                        <div class="col-md-3 mb-2">

                            <button
                                type="submit"
                                class="btn btn-primary">
                                Search
                            </button>

                            <a
                                href="{{ route('admin.submissions.index') }}"
                                class="btn btn-secondary">
                                Reset
                            </a>

                        </div>

                    </div>

                </form>

            </div>

        </div>

        {{-- Bulk Status --}}

        <form
            method="POST"
            action="{{ route('admin.submissions.bulk-status') }}"
            id="bulk-status-form">

            @csrf

            <div class="card shadow">

                <div class="card-body">

                    <div class="row mb-3">

                        <div class="col-md-3">

                            <select
                                name="status"
                                class="form-control"
                                required>

                                <option value="">
                                    Bulk Status
                                </option>

                                <option value="new">
                                    New
                                </option>

                                <option value="read">
                                    Read
                                </option>

                                <option value="replied">
                                    Replied
                                </option>

                                <option value="closed">
                                    Closed
                                </option>

                            </select>

                        </div>

                        <div class="col-md-3">

                            <button
                                type="submit"
                                class="btn btn-warning"
                                onclick="return confirm('Update selected submissions?')">
                                Update Selected
                            </button>

                        </div>

                    </div>

                    <div class="table-responsive">

                        <table class="table table-bordered table-hover">

                            <thead>

                                <tr>

                                    <th>
                                        <input
                                            type="checkbox"
                                            id="select-all">
                                    </th>

                                    <th>ID</th>

                                    <th>Name</th>

                                    <th>Email</th>

                                    <th>Phone</th>

                                    <th>Subject</th>

                                    <th>reCAPTCHA</th>

                                    <th>Status</th>

                                    <th>Priority</th>

                                    <th>Read</th>

                                    <th>Created</th>

                                    <th>Action</th>

                                </tr>

                            </thead>

                            <tbody>

                                @forelse($submissions as $submission)

                                <tr
                                    class="{{ !$submission->is_read ? 'unread-row' : '' }}">

                                    <td>

                                        <input
                                            type="checkbox"
                                            name="ids[]"
                                            value="{{ $submission->id }}"
                                            class="submission-checkbox">

                                    </td>

                                    <td>
                                        {{ $submission->id }}
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
                                        {{ $submission->subject }}
                                    </td>

                                    <td>

                                        @if($submission->recaptcha_verified)

                                        <span class="badge badge-success">
                                            Passed
                                        </span>

                                        @else

                                        <span class="badge badge-danger">
                                            Failed
                                        </span>

                                        @endif

                                    </td>

                                    <td>

                                        <span
                                            class="badge status-{{ $submission->status }}">
                                            {{ ucfirst($submission->status) }}
                                        </span>

                                    </td>

                                    <td>

                                        <span
                                            class="priority-{{ $submission->priority }}">
                                            {{ ucfirst($submission->priority) }}
                                        </span>

                                    </td>

                                    <td>

                                        @if($submission->is_read)

                                        <span class="badge badge-success">
                                            Read
                                        </span>

                                        @else

                                        <span class="badge badge-danger">
                                            Unread
                                        </span>

                                        @endif

                                    </td>

                                    <td>
                                        {{ $submission->created_at->format('d-m-Y H:i') }}
                                    </td>

                                    <td>

                                        <a
                                            href="{{ route('admin.submissions.show', $submission->id) }}"
                                            class="btn btn-sm btn-primary">
                                            View
                                        </a>

                                        <form
                                            method="POST"
                                            action="{{ route('admin.submissions.toggle-read', $submission->id) }}"
                                            class="d-inline">

                                            @csrf

                                            @method('PATCH')

                                            <button
                                                type="submit"
                                                class="btn btn-sm btn-info">
                                                {{ $submission->is_read ? 'Unread' : 'Read' }}
                                            </button>

                                        </form>

                                    </td>

                                </tr>

                                @empty

                                <tr>

                                    <td
                                        colspan="12"
                                        class="text-center">
                                        No submissions found.
                                    </td>

                                </tr>

                                @endforelse

                            </tbody>

                        </table>

                    </div>

                    <div class="mt-3">

                        {{ $submissions->links() }}

                    </div>

                </div>

            </div>

        </form>

    </div>

    <script>
        document
            .getElementById('select-all')
            .addEventListener('change', function() {

                document
                    .querySelectorAll('.submission-checkbox')
                    .forEach(function(checkbox) {

                        checkbox.checked =
                            this.checked;

                    }, this);

            });
    </script>

</body>

</html>