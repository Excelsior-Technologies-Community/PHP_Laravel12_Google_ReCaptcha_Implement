<!DOCTYPE html>
<html lang="en">

<head>
    <title>Laravel Google reCAPTCHA V2 Contact Form</title>

    <meta charset="utf-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/css/bootstrap.min.css"
    />

    <script src="https://www.google.com/recaptcha/api.js"></script>

    <style>
        body {
            background: #f5f7fa;
        }

        .contact-card {
            border: none;
            border-radius: 12px;
            overflow: hidden;
        }

        .card-header {
            padding: 20px;
        }

        .form-control {
            border-radius: 8px;
        }

        .btn {
            border-radius: 8px;
        }

        .captcha-box {
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
        }

        .dashboard-link {
            margin-left: 10px;
        }
    </style>
</head>

<body>

<div class="container py-5">

    <div class="row justify-content-center">

        <div class="col-lg-9 col-md-11">

            <div class="card shadow contact-card">

                <div class="card-header bg-primary text-white">

                    <h3 class="mb-1">
                        Contact Us
                    </h3>

                    <small>
                        Protected by Google reCAPTCHA
                    </small>

                </div>

                <div class="card-body p-4">

                    {{-- Success Message --}}
                    @if(session('success'))

                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>

                    @endif


                    {{-- General Error Message --}}
                    @if($errors->any())

                        <div class="alert alert-danger">

                            <strong>
                                Please correct the errors below.
                            </strong>

                        </div>

                    @endif


                    <form
                        method="POST"
                        action="{{ route('contact.us.store') }}"
                    >

                        @csrf


                        {{-- Name & Email --}}
                        <div class="row">

                            <div class="col-md-6 mb-3">

                                <label>
                                    <strong>Name</strong>
                                </label>

                                <input
                                    type="text"
                                    name="name"
                                    class="form-control"
                                    value="{{ old('name') }}"
                                    placeholder="Enter your name"
                                    required
                                >

                                @error('name')
                                    <small class="text-danger">
                                        {{ $message }}
                                    </small>
                                @enderror

                            </div>


                            <div class="col-md-6 mb-3">

                                <label>
                                    <strong>Email</strong>
                                </label>

                                <input
                                    type="email"
                                    name="email"
                                    class="form-control"
                                    value="{{ old('email') }}"
                                    placeholder="Enter your email"
                                    required
                                >

                                @error('email')
                                    <small class="text-danger">
                                        {{ $message }}
                                    </small>
                                @enderror

                            </div>

                        </div>


                        {{-- Phone & Subject --}}
                        <div class="row">

                            <div class="col-md-6 mb-3">

                                <label>
                                    <strong>Phone</strong>
                                </label>

                                <input
                                    type="text"
                                    name="phone"
                                    class="form-control"
                                    value="{{ old('phone') }}"
                                    placeholder="10 digit phone number"
                                    maxlength="10"
                                    required
                                >

                                @error('phone')
                                    <small class="text-danger">
                                        {{ $message }}
                                    </small>
                                @enderror

                            </div>


                            <div class="col-md-6 mb-3">

                                <label>
                                    <strong>Subject</strong>
                                </label>

                                <input
                                    type="text"
                                    name="subject"
                                    class="form-control"
                                    value="{{ old('subject') }}"
                                    placeholder="Enter subject"
                                    required
                                >

                                @error('subject')
                                    <small class="text-danger">
                                        {{ $message }}
                                    </small>
                                @enderror

                            </div>

                        </div>


                        {{-- Message --}}
                        <div class="mb-3">

                            <label>
                                <strong>Message</strong>
                            </label>

                            <textarea
                                name="message"
                                rows="5"
                                class="form-control"
                                placeholder="Enter your message"
                                required
                            >{{ old('message') }}</textarea>

                            @error('message')
                                <small class="text-danger">
                                    {{ $message }}
                                </small>
                            @enderror

                        </div>


                        {{-- reCAPTCHA --}}
                        <div class="captcha-box mb-3">

                            <label>
                                <strong>Human Verification</strong>
                            </label>

                            <div class="mt-2">

                                <div
                                    class="g-recaptcha"
                                    data-sitekey="{{ config('services.recaptcha.key') }}"
                                ></div>

                            </div>

                            @error('g-recaptcha-response')

                                <small class="text-danger d-block mt-2">
                                    {{ $message }}
                                </small>

                            @enderror

                        </div>


                        {{-- Submit --}}
                        <div class="text-center">

                            <button
                                type="submit"
                                class="btn btn-success px-5"
                            >
                                Submit Securely
                            </button>

                            <a
                                href="{{ route('security.dashboard') }}"
                                class="btn btn-outline-primary dashboard-link"
                            >
                                Security Dashboard
                            </a>

                        </div>

                    </form>

                </div>

            </div>

        </div>

    </div>

</div>

</body>

</html>