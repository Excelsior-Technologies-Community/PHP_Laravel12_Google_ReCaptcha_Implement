<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1">

    <title>
        Submission #{{ $submission->id }}
    </title>

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

        .info-label {
            font-weight: bold;
            color: #555;
        }
    </style>

</head>

<body>

    <div class="container py-5">

        <div class="d-flex justify-content-between mb-4">

            <h3>
                Contact Submission #{{ $submission->id }}
            </h3>

            <a
                href="{{ route('admin.submissions.index') }}"
                class="btn btn-secondary">
                Back
            </a>

        </div>

        @if(session('success'))

        <div class="alert alert-success">
            {{ session('success') }}
        </div>

        @endif

        <div class="row">

            {{-- Main Information --}}

            <div class="col-md-8">

                <div class="card shadow mb-4">

                    <div class="card-header">

                        <h5 class="mb-0">
                            Submission Information
                        </h5>

                    </div>

                    <div class="card-body">

                        <p>
                            <span class="info-label">
                                Name:
                            </span>

                            {{ $submission->name }}
                        </p>

                        <p>
                            <span class="info-label">
                                Email:
                            </span>

                            {{ $submission->email }}
                        </p>

                        <p>
                            <span class="info-label">
                                Phone:
                            </span>

                            {{ $submission->phone }}
                        </p>

                        <p>
                            <span class="info-label">
                                Subject:
                            </span>

                            {{ $submission->subject }}
                        </p>

                        <p>
                            <span class="info-label">
                                Message:
                            </span>
                        </p>

                        <div class="alert alert-light">
                            {!! nl2br(e($submission->message)) !!}
                        </div>

                        <p>
                            <span class="info-label">
                                IP Address:
                            </span>

                            {{ $submission->ip_address }}
                        </p>

                        <p>
                            <span class="info-label">
                                reCAPTCHA:
                            </span>

                            @if($submission->recaptcha_verified)

                            <span class="badge badge-success">
                                Verified
                            </span>

                            @else

                            <span class="badge badge-danger">
                                Failed
                            </span>

                            @endif

                        </p>

                        <p>
                            <span class="info-label">
                                reCAPTCHA Version:
                            </span>

                            {{ strtoupper($submission->recaptcha_version ?? 'N/A') }}
                        </p>

                        <p>
                            <span class="info-label">
                                Language:
                            </span>

                            {{ $submission->language ?? 'en' }}
                        </p>

                        <p>
                            <span class="info-label">
                                Created:
                            </span>

                            {{ $submission->created_at->format('d-m-Y H:i:s') }}
                        </p>

                        @if($submission->attachment_path)

                        <a
                            href="{{ asset('storage/' . $submission->attachment_path) }}"
                            target="_blank"
                            class="btn btn-outline-primary">
                            View Attachment
                        </a>

                        @endif

                    </div>

                </div>

            </div>

            {{-- Management --}}

            <div class="col-md-4">

                {{-- Status --}}

                <div class="card shadow mb-4">

                    <div class="card-header">

                        <strong>
                            Status
                        </strong>

                    </div>

                    <div class="card-body">

                        <form
                            method="POST"
                            action="{{ route('admin.submissions.status', $submission->id) }}">

                            @csrf

                            @method('PATCH')

                            <select
                                name="status"
                                class="form-control mb-2">

                                <option
                                    value="new"
                                    {{ $submission->status === 'new' ? 'selected' : '' }}>
                                    New
                                </option>

                                <option
                                    value="read"
                                    {{ $submission->status === 'read' ? 'selected' : '' }}>
                                    Read
                                </option>

                                <option
                                    value="replied"
                                    {{ $submission->status === 'replied' ? 'selected' : '' }}>
                                    Replied
                                </option>

                                <option
                                    value="closed"
                                    {{ $submission->status === 'closed' ? 'selected' : '' }}>
                                    Closed
                                </option>

                            </select>

                            <button
                                class="btn btn-primary btn-block">
                                Update Status
                            </button>

                        </form>

                    </div>

                </div>

                {{-- Priority --}}

                <div class="card shadow mb-4">

                    <div class="card-header">

                        <strong>
                            Priority
                        </strong>

                    </div>

                    <div class="card-body">

                        <form
                            method="POST"
                            action="{{ route('admin.submissions.priority', $submission->id) }}">

                            @csrf

                            @method('PATCH')

                            <select
                                name="priority"
                                class="form-control mb-2">

                                <option
                                    value="low"
                                    {{ $submission->priority === 'low' ? 'selected' : '' }}>
                                    Low
                                </option>

                                <option
                                    value="medium"
                                    {{ $submission->priority === 'medium' ? 'selected' : '' }}>
                                    Medium
                                </option>

                                <option
                                    value="high"
                                    {{ $submission->priority === 'high' ? 'selected' : '' }}>
                                    High
                                </option>

                            </select>

                            <button
                                class="btn btn-warning btn-block">
                                Update Priority
                            </button>

                        </form>

                    </div>

                </div>

                {{-- Admin Note --}}

                <div class="card shadow mb-4">

                    <div class="card-header">

                        <strong>
                            Internal Admin Note
                        </strong>

                    </div>

                    <div class="card-body">

                        <form
                            method="POST"
                            action="{{ route('admin.submissions.note', $submission->id) }}">

                            @csrf

                            @method('PATCH')

                            <textarea
                                name="admin_note"
                                class="form-control mb-2"
                                rows="6"
                                placeholder="Write internal note...">{{ $submission->admin_note }}</textarea>

                            <button
                                class="btn btn-success btn-block">
                                Save Note
                            </button>

                        </form>

                    </div>

                </div>

                {{-- Read Status --}}

                <div class="card shadow">

                    <div class="card-body text-center">

                        @if($submission->is_read)

                        <p class="text-success">
                            ✓ This submission has been read.
                        </p>

                        @else

                        <p class="text-danger">
                            ● This submission is unread.
                        </p>

                        @endif

                        <form
                            method="POST"
                            action="{{ route('admin.submissions.toggle-read', $submission->id) }}">

                            @csrf

                            @method('PATCH')

                            <button
                                class="btn btn-info btn-block">
                                Mark as
                                {{ $submission->is_read ? 'Unread' : 'Read' }}
                            </button>

                        </form>

                    </div>

                </div>

            </div>

        </div>

    </div>

</body>

</html>