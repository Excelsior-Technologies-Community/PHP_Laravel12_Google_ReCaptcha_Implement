<!DOCTYPE html>
<html lang="en">

<head>
    <title>Laravel Google reCAPTCHA Contact Form</title>

    <meta charset="utf-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/css/bootstrap.min.css"
    />

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

        .honeypot {
            position: absolute;
            left: -9999px;
        }

        .spinner-border {
            display: none;
            width: 1rem;
            height: 1rem;
            margin-right: 0.5rem;
        }

        .btn-loading .spinner-border {
            display: inline-block;
        }

        .btn-loading {
            pointer-events: none;
            opacity: 0.7;
        }

        #form-messages {
            display: none;
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

                    <div id="form-messages"></div>

                    <form
                        id="contact-form"
                        method="POST"
                        action="{{ route('contact.us.store') }}"
                        enctype="multipart/form-data"
                    >

                        @csrf

                        <input type="hidden" name="language" value="en">

                        {{-- Honeypot --}}
                        <div class="honeypot" aria-hidden="true">
                            <label for="honeypot">Leave this empty</label>
                            <input type="text" name="honeypot" id="honeypot" tabindex="-1" autocomplete="off">
                        </div>

                        {{-- Name & Email --}}
                        <div class="row">

                            <div class="col-md-6 mb-3">

                                <label>
                                    <strong>Name</strong>
                                </label>

                                <input
                                    type="text"
                                    name="name"
                                    id="name"
                                    class="form-control"
                                    placeholder="Enter your name"
                                    required
                                >

                                <small class="text-danger field-error" data-field="name"></small>

                            </div>

                            <div class="col-md-6 mb-3">

                                <label>
                                    <strong>Email</strong>
                                </label>

                                <input
                                    type="email"
                                    name="email"
                                    id="email"
                                    class="form-control"
                                    placeholder="Enter your email"
                                    required
                                >

                                <small class="text-danger field-error" data-field="email"></small>

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
                                    id="phone"
                                    class="form-control"
                                    placeholder="10 digit phone number"
                                    maxlength="10"
                                    required
                                >

                                <small class="text-danger field-error" data-field="phone"></small>

                            </div>

                            <div class="col-md-6 mb-3">

                                <label>
                                    <strong>Subject</strong>
                                </label>

                                <input
                                    type="text"
                                    name="subject"
                                    id="subject"
                                    class="form-control"
                                    placeholder="Enter subject"
                                    required
                                >

                                <small class="text-danger field-error" data-field="subject"></small>

                            </div>

                        </div>

                        {{-- Message --}}
                        <div class="mb-3">

                            <label>
                                <strong>Message</strong>
                            </label>

                            <textarea
                                name="message"
                                id="message"
                                rows="5"
                                class="form-control"
                                placeholder="Enter your message"
                                required
                            ></textarea>

                            <small class="text-danger field-error" data-field="message"></small>

                        </div>

                        {{-- Attachment --}}
                        <div class="mb-3">

                            <label>
                                <strong>Attachment</strong>
                            </label>

                            <input
                                type="file"
                                name="attachment"
                                id="attachment"
                                class="form-control"
                                accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                            >

                            <small class="text-danger field-error" data-field="attachment"></small>

                        </div>

                        {{-- reCAPTCHA Version Toggle --}}
                        <div class="mb-3">

                            <label>
                                <strong>reCAPTCHA Version</strong>
                            </label>

                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="recaptcha_version" id="recaptcha_v2" value="v2" checked>
                                <label class="form-check-label" for="recaptcha_v2">v2</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="recaptcha_version" id="recaptcha_v3" value="v3">
                                <label class="form-check-label" for="recaptcha_v3">v3</label>
                            </div>

                        </div>

                        {{-- reCAPTCHA --}}
                        <div class="captcha-box mb-3" id="captcha-container">

                            <label>
                                <strong>Human Verification</strong>
                            </label>

                            <div class="mt-2" id="recaptcha-element">
                                <div class="g-recaptcha" data-sitekey="{{ config('services.recaptcha.key') }}"></div>
                            </div>

                            <small class="text-danger field-error" data-field="g-recaptcha-response"></small>

                        </div>

                        {{-- Submit --}}
                        <div class="text-center">

                            <button
                                type="submit"
                                id="submit-btn"
                                class="btn btn-success px-5"
                            >
                                <span class="spinner-border" role="status" aria-hidden="true"></span>
                                <span>Submit Securely</span>
                            </button>

                            <a
                                 href="{{ route('admin.dashboard') }}"
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

<script src="https://www.google.com/recaptcha/api.js" async defer></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('contact-form');
        const submitBtn = document.getElementById('submit-btn');
        const formMessages = document.getElementById('form-messages');
        const recaptchaContainer = document.getElementById('recaptcha-element');
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        let draftTimer = null;

        function showMessage(message, type = 'success') {
            formMessages.className = `alert alert-${type}`;
            formMessages.textContent = message;
            formMessages.style.display = 'block';
        }

        function clearMessages() {
            formMessages.style.display = 'none';
            formMessages.className = '';
            document.querySelectorAll('.field-error').forEach(function (el) {
                el.textContent = '';
            });
        }

        function setLoading(loading) {
            if (loading) {
                submitBtn.classList.add('btn-loading');
                submitBtn.disabled = true;
            } else {
                submitBtn.classList.remove('btn-loading');
                submitBtn.disabled = false;
            }
        }

        function loadRecaptcha() {
            const version = document.querySelector('input[name="recaptcha_version"]:checked').value;
            const siteKey = '{{ config('services.recaptcha.key') }}';

            if (version === 'v2') {
                recaptchaContainer.innerHTML = '<div class="g-recaptcha" data-sitekey="' + siteKey + '"></div>';
                if (typeof grecaptcha !== 'undefined') {
                    grecaptcha.render(recaptchaContainer.querySelector('.g-recaptcha'), { sitekey: siteKey });
                }
            } else {
                recaptchaContainer.innerHTML = '<input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response">';
            }
        }

        document.querySelectorAll('input[name="recaptcha_version"]').forEach(function (radio) {
            radio.addEventListener('change', loadRecaptcha);
        });

        loadRecaptcha();

        function restoreDraft() {
            const draft = localStorage.getItem('contact_draft');
            if (!draft) return;

            try {
                const data = JSON.parse(draft);
                if (data.name) document.getElementById('name').value = data.name;
                if (data.email) document.getElementById('email').value = data.email;
                if (data.phone) document.getElementById('phone').value = data.phone;
                if (data.subject) document.getElementById('subject').value = data.subject;
                if (data.message) document.getElementById('message').value = data.message;
                if (data.language) document.querySelector('input[name="language"]').value = data.language;
            } catch (e) {
                console.error('Failed to restore draft', e);
            }
        }

        function saveDraft() {
            const draft = {
                name: document.getElementById('name').value,
                email: document.getElementById('email').value,
                phone: document.getElementById('phone').value,
                subject: document.getElementById('subject').value,
                message: document.getElementById('message').value,
                language: document.querySelector('input[name="language"]').value,
                saved_at: new Date().toISOString()
            };

            localStorage.setItem('contact_draft', JSON.stringify(draft));
        }

        function startDraftTimer() {
            if (draftTimer) clearInterval(draftTimer);
            draftTimer = setInterval(saveDraft, 30000);
        }

        restoreDraft();
        startDraftTimer();

        form.addEventListener('submit', function (e) {
            e.preventDefault();
            clearMessages();
            setLoading(true);

            const formData = new FormData(form);
            const version = document.querySelector('input[name="recaptcha_version"]:checked').value;

            if (version === 'v3') {
                grecaptcha.ready(function () {
                    grecaptcha.execute('{{ config('services.recaptcha.key') }}', { action: 'submit' }).then(function (token) {
                        formData.set('g-recaptcha-response', token);
                        submitForm(formData);
                    });
                });
            } else {
                const recaptchaResponse = grecaptcha.getResponse();
                if (!recaptchaResponse) {
                    showMessage('Please complete the reCAPTCHA.', 'danger');
                    setLoading(false);
                    return;
                }
                formData.set('g-recaptcha-response', recaptchaResponse);
                submitForm(formData);
            }
        });

        function submitForm(formData) {
            fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(function (response) {
                return response.json().then(function (data) {
                    if (!response.ok) {
                        var err = new Error(data.message || 'Something went wrong');
                        err.status = response.status;
                        err.errors = data.errors;
                        throw err;
                    }
                    return data;
                });
            })
            .then(function (data) {
                showMessage(data.message || 'Success!', 'success');
                localStorage.removeItem('contact_draft');
                form.reset();
                if (typeof grecaptcha !== 'undefined') {
                    grecaptcha.reset();
                }
                loadRecaptcha();
            })
            .catch(function (error) {
                if (error.status === 422 && error.errors) {
                    Object.keys(error.errors).forEach(function (field) {
                        var errorEl = document.querySelector('.field-error[data-field="' + field + '"]');
                        if (errorEl) {
                            errorEl.textContent = error.errors[field][0];
                        }
                    });
                    showMessage(error.message || 'Please correct the errors.', 'danger');
                } else if (error.status === 403) {
                    showMessage(error.message || 'Your IP is blocked.', 'danger');
                } else {
                    showMessage(error.message || 'Something went wrong.', 'danger');
                }
            })
            .finally(function () {
                setLoading(false);
            });
        }
    });
</script>

</body>

</html>
