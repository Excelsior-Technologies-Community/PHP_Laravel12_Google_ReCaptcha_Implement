<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>New Contact Form Submission</title>
</head>
<body>
    <h2>New Contact Form Submission</h2>

    <p><strong>Name:</strong> {{ $submission->name }}</p>
    <p><strong>Email:</strong> {{ $submission->email }}</p>
    <p><strong>Phone:</strong> {{ $submission->phone }}</p>
    <p><strong>Subject:</strong> {{ $submission->subject }}</p>

    <p><strong>Message:</strong></p>
    <p>{{ $submission->message }}</p>

    <p><strong>IP Address:</strong> {{ $submission->ip_address }}</p>
    <p><strong>reCAPTCHA Verified:</strong> {{ $submission->recaptcha_verified ? 'Yes' : 'No' }}</p>
    <p><strong>reCAPTCHA Version:</strong> {{ $submission->recaptcha_version ?? 'v2' }}</p>
    <p><strong>Language:</strong> {{ $submission->language ?? 'en' }}</p>
    <p><strong>Submitted At:</strong> {{ $submission->created_at->format('d M Y H:i:s') }}</p>

    @if($submission->attachment_path)
        <p><strong>Attachment:</strong> <a href="{{ asset('storage/' . $submission->attachment_path) }}">Download</a></p>
    @endif
</body>
</html>