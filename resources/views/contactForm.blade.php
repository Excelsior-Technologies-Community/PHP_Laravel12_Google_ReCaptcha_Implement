<!DOCTYPE html>
<html>
<head>
    <title>Laravel Google ReCaptcha V2 Example</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap for UI styling -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/css/bootstrap.min.css" />

    <!-- Google reCAPTCHA Script (loads v2 checkbox widget) -->
    <script src="https://www.google.com/recaptcha/api.js"></script>
</head>

<body>
    <div class="container mt-5">
        <div class="col-10 offset-1">
            <div class="card">

                <div class="card-header bg-primary">
                    <!-- Page Title -->
                    <h3 class="text-white">Laravel Google reCAPTCHA V2 Example</h3>
                </div>

                <div class="card-body">

                    <!-- Contact Form -->
                    <form method="POST" action="{{ route('contact.us.store') }}">
                        @csrf

                        <!-- Name & Email Inputs -->
                        <div class="row">
                            <div class="col-md-6">
                                <strong>Name:</strong>
                                <!-- Name input field -->
                                <input type="text" name="name" class="form-control" value="{{ old('name') }}">
                                <!-- Name validation error -->
                                @error('name') 
                                    <span class="text-danger">{{ $message }}</span> 
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <strong>Email:</strong>
                                <!-- Email input field -->
                                <input type="text" name="email" class="form-control" value="{{ old('email') }}">
                                <!-- Email validation error -->
                                @error('email') 
                                    <span class="text-danger">{{ $message }}</span> 
                                @enderror
                            </div>
                        </div>

                        <!-- Phone & Subject Inputs -->
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <strong>Phone:</strong>
                                <!-- Phone number field -->
                                <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
                                <!-- Phone validation error -->
                                @error('phone') 
                                    <span class="text-danger">{{ $message }}</span> 
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <strong>Subject:</strong>
                                <!-- Subject input field -->
                                <input type="text" name="subject" class="form-control" value="{{ old('subject') }}">
                                <!-- Subject validation error -->
                                @error('subject') 
                                    <span class="text-danger">{{ $message }}</span> 
                                @enderror
                            </div>
                        </div>

                        <!-- Message Textarea -->
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <strong>Message:</strong>
                                <!-- Message input field -->
                                <textarea name="message" rows="3" class="form-control">{{ old('message') }}</textarea>
                                <!-- Message validation error -->
                                @error('message') 
                                    <span class="text-danger">{{ $message }}</span> 
                                @enderror
                            </div>
                        </div>

                        <!-- Google reCAPTCHA Widget -->
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <strong>ReCaptcha:</strong>

                                <!-- Google reCAPTCHA Checkbox 
                                     - data-sitekey loads the site key from .env file 
                                     - User must complete this to submit the form 
                                -->
                                <div class="g-recaptcha" data-sitekey="{{ env('GOOGLE_RECAPTCHA_KEY') }}"></div>

                                <!-- reCAPTCHA validation error -->
                                @error('g-recaptcha-response') 
                                    <span class="text-danger">{{ $message }}</span> 
                                @enderror
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="text-center mt-4">
                            <button class="btn btn-success">Submit</button>
                        </div>

                    </form>
                    <!-- End Contact Form -->

                </div>
            </div>
        </div>
    </div>
</body>
</html>
