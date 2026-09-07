<!DOCTYPE html>
<html lang="en">

<head>

    <title>Submission Details</title>

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

    <div class="d-flex justify-content-between mb-4">

        <h2>
            Contact Submission
        </h2>

        <a
            href="{{ route('security.dashboard') }}"
            class="btn btn-secondary"
        >
            Back to Dashboard
        </a>

    </div>


    <div class="card shadow-sm">

        <div class="card-header bg-primary text-white">

            <h5 class="mb-0">
                Submission #{{ $submission->id }}
            </h5>

        </div>


        <div class="card-body">

            <div class="row">

                <div class="col-md-6 mb-3">

                    <strong>Name</strong>

                    <p>
                        {{ $submission->name }}
                    </p>

                </div>


                <div class="col-md-6 mb-3">

                    <strong>Email</strong>

                    <p>
                        {{ $submission->email }}
                    </p>

                </div>


                <div class="col-md-6 mb-3">

                    <strong>Phone</strong>

                    <p>
                        {{ $submission->phone }}
                    </p>

                </div>


                <div class="col-md-6 mb-3">

                    <strong>Subject</strong>

                    <p>
                        {{ $submission->subject }}
                    </p>

                </div>


                <div class="col-md-12 mb-3">

                    <strong>Message</strong>

                    <div class="border rounded p-3">
                        {{ $submission->message }}
                    </div>

                </div>


                <div class="col-md-6 mb-3">

                    <strong>IP Address</strong>

                    <p>
                        {{ $submission->ip_address }}
                    </p>

                </div>


                <div class="col-md-6 mb-3">

                    <strong>reCAPTCHA Status</strong>

                    <p>

                        @if($submission->recaptcha_verified)

                            <span class="badge badge-success">
                                Verified Human
                            </span>

                        @else

                            <span class="badge badge-danger">
                                Verification Failed
                            </span>

                        @endif

                    </p>

                </div>


                <div class="col-md-12 mb-3">

                    <strong>User Agent</strong>

                    <p class="text-muted">
                        {{ $submission->user_agent }}
                    </p>

                </div>


                <div class="col-md-12">

                    <strong>Submitted At</strong>

                    <p>
                        {{ $submission->created_at->format('d M Y H:i:s') }}
                    </p>

                </div>

            </div>

        </div>

    </div>

</div>

</body>

</html>