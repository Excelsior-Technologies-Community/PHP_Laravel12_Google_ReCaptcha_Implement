<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1">

    <meta
        name="csrf-token"
        content="{{ csrf_token() }}">

    <title>Laravel Google reCAPTCHA Contact Form</title>

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/css/bootstrap.min.css">

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

        .character-counter {
            text-align: right;
            font-size: 13px;
            color: #6c757d;
            margin-top: 5px;
        }

        .draft-status {
            font-size: 13px;
            color: #28a745;
            margin-top: 5px;
        }

        .file-info {
            font-size: 13px;
            margin-top: 5px;
        }

        .phone-valid {
            border-color: #28a745 !important;
        }

        .phone-invalid {
            border-color: #dc3545 !important;
        }

        .required-star {
            color: #dc3545;
        }

        #recaptcha-element {
            min-height: 78px;
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
                            enctype="multipart/form-data">

                            @csrf

                            <input
                                type="hidden"
                                name="language"
                                value="en">

                            {{-- Honeypot --}}
                            <div
                                class="honeypot"
                                aria-hidden="true">

                                <label for="honeypot">
                                    Leave this empty
                                </label>

                                <input
                                    type="text"
                                    name="honeypot"
                                    id="honeypot"
                                    tabindex="-1"
                                    autocomplete="off">

                            </div>

                            {{-- Name & Email --}}
                            <div class="row">

                                <div class="col-md-6 mb-3">

                                    <label>
                                        <strong>Name</strong>
                                        <span class="required-star">*</span>
                                    </label>

                                    <input
                                        type="text"
                                        name="name"
                                        id="name"
                                        class="form-control"
                                        placeholder="Enter your name"
                                        maxlength="100"
                                        required>

                                    <small
                                        class="text-danger field-error"
                                        data-field="name"></small>

                                </div>

                                <div class="col-md-6 mb-3">

                                    <label>
                                        <strong>Email</strong>
                                        <span class="required-star">*</span>
                                    </label>

                                    <input
                                        type="email"
                                        name="email"
                                        id="email"
                                        class="form-control"
                                        placeholder="Enter your email"
                                        maxlength="255"
                                        required>

                                    <small
                                        class="text-danger field-error"
                                        data-field="email"></small>

                                </div>

                            </div>

                            {{-- Phone & Subject --}}
                            <div class="row">

                                <div class="col-md-6 mb-3">

                                    <label>
                                        <strong>Phone</strong>
                                        <span class="required-star">*</span>
                                    </label>

                                    <input
                                        type="text"
                                        name="phone"
                                        id="phone"
                                        class="form-control"
                                        placeholder="10 digit phone number"
                                        maxlength="10"
                                        inputmode="numeric"
                                        required>

                                    <small
                                        class="text-muted"
                                        id="phone-help">
                                        Enter exactly 10 digits.
                                    </small>

                                    <br>

                                    <small
                                        class="text-danger field-error"
                                        data-field="phone"></small>

                                </div>

                                <div class="col-md-6 mb-3">

                                    <label>
                                        <strong>Subject</strong>
                                        <span class="required-star">*</span>
                                    </label>

                                    <input
                                        type="text"
                                        name="subject"
                                        id="subject"
                                        class="form-control"
                                        placeholder="Enter subject"
                                        maxlength="255"
                                        required>

                                    <small
                                        class="text-danger field-error"
                                        data-field="subject"></small>

                                </div>

                            </div>

                            {{-- Message --}}
                            <div class="mb-3">

                                <label>
                                    <strong>Message</strong>
                                    <span class="required-star">*</span>
                                </label>

                                <textarea
                                    name="message"
                                    id="message"
                                    rows="5"
                                    class="form-control"
                                    placeholder="Enter your message"
                                    maxlength="5000"
                                    required></textarea>

                                <div class="character-counter">

                                    <span id="message-count">
                                        0
                                    </span>

                                    / 5000 characters

                                </div>

                                <small
                                    class="text-danger field-error"
                                    data-field="message"></small>

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
                                    accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">

                                <div
                                    class="file-info"
                                    id="file-info">

                                    Maximum file size: 5 MB

                                </div>

                                <small
                                    class="text-danger field-error"
                                    data-field="attachment"></small>

                            </div>

                            {{-- reCAPTCHA Version --}}
                            <div class="mb-3">

                                <label>
                                    <strong>reCAPTCHA Version</strong>
                                </label>

                                <div class="form-check form-check-inline">

                                    <input
                                        class="form-check-input"
                                        type="radio"
                                        name="recaptcha_version"
                                        id="recaptcha_v2"
                                        value="v2"
                                        checked>

                                    <label
                                        class="form-check-label"
                                        for="recaptcha_v2">

                                        v2

                                    </label>

                                </div>

                                <div class="form-check form-check-inline">

                                    <input
                                        class="form-check-input"
                                        type="radio"
                                        name="recaptcha_version"
                                        id="recaptcha_v3"
                                        value="v3">

                                    <label
                                        class="form-check-label"
                                        for="recaptcha_v3">

                                        v3

                                    </label>

                                </div>

                            </div>

                            {{-- reCAPTCHA --}}
                            <div
                                class="captcha-box mb-3"
                                id="captcha-container">

                                <label>
                                    <strong>
                                        Human Verification
                                    </strong>
                                </label>

                                <div
                                    class="mt-2"
                                    id="recaptcha-element">
                                </div>

                                <small
                                    class="text-danger field-error"
                                    data-field="g-recaptcha-response">
                                </small>

                            </div>

                            {{-- Draft Status --}}
                            <div
                                class="draft-status mb-3"
                                id="draft-status">

                                Draft auto-save is enabled.

                            </div>

                            {{-- Buttons --}}
                            <div class="text-center">

                                <button
                                    type="submit"
                                    id="submit-btn"
                                    class="btn btn-success px-5">

                                    <span
                                        class="spinner-border"
                                        role="status"
                                        aria-hidden="true">
                                    </span>

                                    <span>
                                        Submit Securely
                                    </span>

                                </button>

                                <button
                                    type="button"
                                    id="clear-btn"
                                    class="btn btn-outline-danger">

                                    Clear Form

                                </button>

                                <a
                                    href="{{ route('admin.dashboard') }}"
                                    class="btn btn-outline-primary dashboard-link">

                                    Security Dashboard

                                </a>

                            </div>

                        </form>

                    </div>

                </div>

            </div>

        </div>

    </div>

    <script
        src="https://www.google.com/recaptcha/api.js?render=explicit"
        async
        defer>
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            /*
            |--------------------------------------------------------------------------
            | Elements
            |--------------------------------------------------------------------------
            */

            const form =
                document.getElementById('contact-form');

            const submitBtn =
                document.getElementById('submit-btn');

            const clearBtn =
                document.getElementById('clear-btn');

            const formMessages =
                document.getElementById('form-messages');

            const recaptchaContainer =
                document.getElementById('recaptcha-element');

            const csrfToken =
                document.querySelector(
                    'meta[name="csrf-token"]'
                ).content;

            const messageInput =
                document.getElementById('message');

            const messageCount =
                document.getElementById('message-count');

            const phoneInput =
                document.getElementById('phone');

            const attachmentInput =
                document.getElementById('attachment');

            const fileInfo =
                document.getElementById('file-info');

            const draftStatus =
                document.getElementById('draft-status');

            /*
            |--------------------------------------------------------------------------
            | IMPORTANT: reCAPTCHA Site Key
            |--------------------------------------------------------------------------
            */

            const recaptchaSiteKey =
                @json(config('services.recaptcha.key'));

            console.log(
                'reCAPTCHA Site Key:',
                recaptchaSiteKey
            );

            let recaptchaWidgetId = null;

            let draftTimer = null;

            /*
            |--------------------------------------------------------------------------
            | Show Message
            |--------------------------------------------------------------------------
            */

            function showMessage(
                message,
                type = 'success'
            ) {

                formMessages.className =
                    `alert alert-${type}`;

                formMessages.textContent =
                    message;

                formMessages.style.display =
                    'block';

                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });

            }

            /*
            |--------------------------------------------------------------------------
            | Clear Messages
            |--------------------------------------------------------------------------
            */

            function clearMessages() {

                formMessages.style.display =
                    'none';

                formMessages.className =
                    '';

                document
                    .querySelectorAll('.field-error')
                    .forEach(function(el) {

                        el.textContent = '';

                    });

            }

            /*
            |--------------------------------------------------------------------------
            | Loading
            |--------------------------------------------------------------------------
            */

            function setLoading(loading) {

                if (loading) {

                    submitBtn.classList.add(
                        'btn-loading'
                    );

                    submitBtn.disabled = true;

                } else {

                    submitBtn.classList.remove(
                        'btn-loading'
                    );

                    submitBtn.disabled = false;

                }

            }

            /*
            |--------------------------------------------------------------------------
            | Character Counter
            |--------------------------------------------------------------------------
            */

            function updateMessageCounter() {

                messageCount.textContent =
                    messageInput.value.length;

            }

            messageInput.addEventListener(
                'input',
                updateMessageCounter
            );

            updateMessageCounter();

            /*
            |--------------------------------------------------------------------------
            | Phone Validation
            |--------------------------------------------------------------------------
            */

            phoneInput.addEventListener(
                'input',
                function() {

                    this.value =
                        this.value
                        .replace(/\D/g, '')
                        .slice(0, 10);

                    if (
                        this.value.length === 10
                    ) {

                        this.classList.remove(
                            'phone-invalid'
                        );

                        this.classList.add(
                            'phone-valid'
                        );

                    } else {

                        this.classList.remove(
                            'phone-valid'
                        );

                        if (
                            this.value.length > 0
                        ) {

                            this.classList.add(
                                'phone-invalid'
                            );

                        } else {

                            this.classList.remove(
                                'phone-invalid'
                            );

                        }

                    }

                }
            );

            /*
            |--------------------------------------------------------------------------
            | Attachment Validation
            |--------------------------------------------------------------------------
            */

            attachmentInput.addEventListener(
                'change',
                function() {

                    const file =
                        this.files[0];

                    fileInfo.className =
                        'file-info';

                    if (!file) {

                        fileInfo.textContent =
                            'Maximum file size: 5 MB';

                        return;

                    }

                    const maxSize =
                        5 * 1024 * 1024;

                    const allowedExtensions = [
                        'pdf',
                        'doc',
                        'docx',
                        'jpg',
                        'jpeg',
                        'png'
                    ];

                    const extension =
                        file.name
                        .split('.')
                        .pop()
                        .toLowerCase();

                    if (
                        !allowedExtensions.includes(
                            extension
                        )
                    ) {

                        this.value = '';

                        fileInfo.textContent =
                            'Invalid file type. Allowed: PDF, DOC, DOCX, JPG, JPEG, PNG';

                        fileInfo.className =
                            'file-info text-danger';

                        return;

                    }

                    if (file.size > maxSize) {

                        this.value = '';

                        fileInfo.textContent =
                            'File is too large. Maximum size is 5 MB.';

                        fileInfo.className =
                            'file-info text-danger';

                        return;

                    }

                    fileInfo.textContent =
                        `Selected: ${file.name} (${(
                    file.size /
                    1024 /
                    1024
                ).toFixed(2)} MB)`;

                    fileInfo.className =
                        'file-info text-success';

                }
            );

            /*
            |--------------------------------------------------------------------------
            | Load reCAPTCHA
            |--------------------------------------------------------------------------
            */

            function loadRecaptcha() {

                const versionElement =
                    document.querySelector(
                        'input[name="recaptcha_version"]:checked'
                    );

                if (!versionElement) {
                    return;
                }

                const version =
                    versionElement.value;

                recaptchaContainer.innerHTML =
                    '';

                recaptchaWidgetId =
                    null;

                if (!recaptchaSiteKey) {

                    console.error(
                        'Google reCAPTCHA site key is missing.'
                    );

                    showMessage(
                        'Google reCAPTCHA site key is missing. Please check your configuration.',
                        'danger'
                    );

                    return;

                }

                /*
                |--------------------------------------------------------------------------
                | v2
                |--------------------------------------------------------------------------
                */

                if (version === 'v2') {

                    if (
                        typeof grecaptcha ===
                        'undefined'
                    ) {

                        setTimeout(
                            loadRecaptcha,
                            500
                        );

                        return;

                    }

                    grecaptcha.ready(
                        function() {

                            const element =
                                document.createElement(
                                    'div'
                                );

                            recaptchaContainer.appendChild(
                                element
                            );

                            try {

                                recaptchaWidgetId =
                                    grecaptcha.render(
                                        element, {
                                            sitekey: recaptchaSiteKey
                                        }
                                    );

                            } catch (error) {

                                console.error(
                                    'reCAPTCHA v2 render error:',
                                    error
                                );

                            }

                        }
                    );

                }

                /*
                |--------------------------------------------------------------------------
                | v3
                |--------------------------------------------------------------------------
                */
                else {

                    recaptchaContainer.innerHTML = `
                <div class="alert alert-info mb-0">
                    <small>
                        This form is protected by Google reCAPTCHA v3.
                        No checkbox is required.
                    </small>
                </div>
            `;

                }

            }

            /*
            |--------------------------------------------------------------------------
            | Change reCAPTCHA Version
            |--------------------------------------------------------------------------
            */

            document
                .querySelectorAll(
                    'input[name="recaptcha_version"]'
                )
                .forEach(function(radio) {

                    radio.addEventListener(
                        'change',
                        function() {

                            loadRecaptcha();

                        }
                    );

                });

            /*
            |--------------------------------------------------------------------------
            | Wait for Google reCAPTCHA
            |--------------------------------------------------------------------------
            */

            setTimeout(
                loadRecaptcha,
                1000
            );

            /*
            |--------------------------------------------------------------------------
            | Restore Draft
            |--------------------------------------------------------------------------
            */

            function restoreDraft() {

                const draft =
                    localStorage.getItem(
                        'contact_draft'
                    );

                if (!draft) {
                    return;
                }

                try {

                    const data =
                        JSON.parse(draft);

                    if (data.name) {

                        document.getElementById(
                                'name'
                            ).value =
                            data.name;

                    }

                    if (data.email) {

                        document.getElementById(
                                'email'
                            ).value =
                            data.email;

                    }

                    if (data.phone) {

                        document.getElementById(
                                'phone'
                            ).value =
                            data.phone;

                    }

                    if (data.subject) {

                        document.getElementById(
                                'subject'
                            ).value =
                            data.subject;

                    }

                    if (data.message) {

                        document.getElementById(
                                'message'
                            ).value =
                            data.message;

                    }

                    if (data.language) {

                        document.querySelector(
                                'input[name="language"]'
                            ).value =
                            data.language;

                    }

                    updateMessageCounter();

                    draftStatus.textContent =
                        'Saved draft restored.';

                } catch (error) {

                    console.error(
                        'Failed to restore draft',
                        error
                    );

                }

            }

            /*
            |--------------------------------------------------------------------------
            | Save Draft
            |--------------------------------------------------------------------------
            */

            function saveDraft() {

                const draft = {

                    name: document.getElementById(
                        'name'
                    ).value,

                    email: document.getElementById(
                        'email'
                    ).value,

                    phone: document.getElementById(
                        'phone'
                    ).value,

                    subject: document.getElementById(
                        'subject'
                    ).value,

                    message: document.getElementById(
                        'message'
                    ).value,

                    language: document.querySelector(
                        'input[name="language"]'
                    ).value,

                    saved_at: new Date().toISOString()

                };

                localStorage.setItem(
                    'contact_draft',
                    JSON.stringify(draft)
                );

                draftStatus.textContent =
                    'Draft saved automatically at ' +
                    new Date().toLocaleTimeString();

            }

            /*
            |--------------------------------------------------------------------------
            | Draft Timer
            |--------------------------------------------------------------------------
            */

            function startDraftTimer() {

                if (draftTimer) {

                    clearInterval(
                        draftTimer
                    );

                }

                draftTimer =
                    setInterval(
                        saveDraft,
                        30000
                    );

            }

            restoreDraft();

            startDraftTimer();

            /*
            |--------------------------------------------------------------------------
            | Clear Form
            |--------------------------------------------------------------------------
            */

            clearBtn.addEventListener(
                'click',
                function() {

                    if (
                        !confirm(
                            'Are you sure you want to clear the form and saved draft?'
                        )
                    ) {

                        return;

                    }

                    form.reset();

                    localStorage.removeItem(
                        'contact_draft'
                    );

                    clearMessages();

                    updateMessageCounter();

                    phoneInput.classList.remove(
                        'phone-valid',
                        'phone-invalid'
                    );

                    fileInfo.textContent =
                        'Maximum file size: 5 MB';

                    fileInfo.className =
                        'file-info';

                    draftStatus.textContent =
                        'Draft cleared.';

                    loadRecaptcha();

                }
            );

            /*
            |--------------------------------------------------------------------------
            | Submit Form
            |--------------------------------------------------------------------------
            */

            form.addEventListener(
                'submit',
                function(e) {

                    e.preventDefault();

                    clearMessages();

                    /*
                    |--------------------------------------------------------------------------
                    | Phone Check
                    |--------------------------------------------------------------------------
                    */

                    if (
                        phoneInput.value.length !== 10
                    ) {

                        showMessage(
                            'Please enter a valid 10 digit phone number.',
                            'danger'
                        );

                        phoneInput.focus();

                        return;

                    }

                    /*
                    |--------------------------------------------------------------------------
                    | Attachment Check
                    |--------------------------------------------------------------------------
                    */

                    const file =
                        attachmentInput.files[0];

                    if (file) {

                        if (
                            file.size >
                            5 * 1024 * 1024
                        ) {

                            showMessage(
                                'Attachment must not exceed 5 MB.',
                                'danger'
                            );

                            return;

                        }

                        const allowedExtensions = [
                            'pdf',
                            'doc',
                            'docx',
                            'jpg',
                            'jpeg',
                            'png'
                        ];

                        const extension =
                            file.name
                            .split('.')
                            .pop()
                            .toLowerCase();

                        if (
                            !allowedExtensions.includes(
                                extension
                            )
                        ) {

                            showMessage(
                                'Invalid attachment type.',
                                'danger'
                            );

                            return;

                        }

                    }

                    setLoading(true);

                    const formData =
                        new FormData(form);

                    const version =
                        document.querySelector(
                            'input[name="recaptcha_version"]:checked'
                        ).value;

                    /*
                    |--------------------------------------------------------------------------
                    | v3
                    |--------------------------------------------------------------------------
                    */

                    if (version === 'v3') {

                        if (
                            typeof grecaptcha ===
                            'undefined'
                        ) {

                            showMessage(
                                'reCAPTCHA is still loading. Please try again.',
                                'danger'
                            );

                            setLoading(false);

                            return;

                        }

                        if (!recaptchaSiteKey) {

                            showMessage(
                                'reCAPTCHA site key is missing.',
                                'danger'
                            );

                            setLoading(false);

                            return;

                        }

                        grecaptcha.ready(
                            function() {

                                grecaptcha.execute(
                                        recaptchaSiteKey, {
                                            action: 'submit'
                                        }
                                    )
                                    .then(
                                        function(token) {

                                            formData.set(
                                                'g-recaptcha-response',
                                                token
                                            );

                                            submitForm(
                                                formData
                                            );

                                        }
                                    )
                                    .catch(
                                        function() {

                                            showMessage(
                                                'reCAPTCHA verification could not be completed.',
                                                'danger'
                                            );

                                            setLoading(false);

                                        }
                                    );

                            }
                        );

                    }

                    /*
                    |--------------------------------------------------------------------------
                    | v2
                    |--------------------------------------------------------------------------
                    */
                    else {

                        if (
                            typeof grecaptcha ===
                            'undefined'
                        ) {

                            showMessage(
                                'reCAPTCHA is still loading. Please try again.',
                                'danger'
                            );

                            setLoading(false);

                            return;

                        }

                        if (
                            recaptchaWidgetId ===
                            null
                        ) {

                            showMessage(
                                'reCAPTCHA is not ready. Please wait a moment and try again.',
                                'danger'
                            );

                            setLoading(false);

                            return;

                        }

                        const recaptchaResponse =
                            grecaptcha.getResponse(
                                recaptchaWidgetId
                            );

                        if (!recaptchaResponse) {

                            showMessage(
                                'Please complete the reCAPTCHA.',
                                'danger'
                            );

                            setLoading(false);

                            return;

                        }

                        formData.set(
                            'g-recaptcha-response',
                            recaptchaResponse
                        );

                        submitForm(
                            formData
                        );

                    }

                }
            );

            /*
            |--------------------------------------------------------------------------
            | AJAX Submit
            |--------------------------------------------------------------------------
            */

            function submitForm(formData) {

                fetch(
                        form.action, {
                            method: 'POST',

                            headers: {
                                'X-CSRF-TOKEN': csrfToken,

                                'Accept': 'application/json'
                            },

                            body: formData
                        }
                    )

                    .then(
                        function(response) {

                            return response
                                .json()
                                .then(
                                    function(data) {

                                        if (!response.ok) {

                                            const error =
                                                new Error(
                                                    data.message ||
                                                    'Something went wrong'
                                                );

                                            error.status =
                                                response.status;

                                            error.errors =
                                                data.errors;

                                            throw error;

                                        }

                                        return data;

                                    }
                                );

                        }
                    )

                    .then(
                        function(data) {

                            showMessage(
                                data.message ||
                                'Contact form submitted successfully!',
                                'success'
                            );

                            /*
                            |--------------------------------------------------------------------------
                            | Remove Draft
                            |--------------------------------------------------------------------------
                            */

                            localStorage.removeItem(
                                'contact_draft'
                            );

                            /*
                            |--------------------------------------------------------------------------
                            | Reset Form
                            |--------------------------------------------------------------------------
                            */

                            form.reset();

                            updateMessageCounter();

                            phoneInput.classList.remove(
                                'phone-valid',
                                'phone-invalid'
                            );

                            fileInfo.textContent =
                                'Maximum file size: 5 MB';

                            fileInfo.className =
                                'file-info';

                            draftStatus.textContent =
                                'Form submitted successfully. Draft removed.';

                            /*
                            |--------------------------------------------------------------------------
                            | Reset reCAPTCHA
                            |--------------------------------------------------------------------------
                            */

                            if (
                                typeof grecaptcha !==
                                'undefined' &&
                                recaptchaWidgetId !==
                                null
                            ) {

                                try {

                                    grecaptcha.reset(
                                        recaptchaWidgetId
                                    );

                                } catch (error) {

                                    console.log(
                                        error
                                    );

                                }

                            }

                            loadRecaptcha();

                        }
                    )

                    .catch(
                        function(error) {

                            console.error(
                                'Submission error:',
                                error
                            );

                            if (
                                error.status === 422 &&
                                error.errors
                            ) {

                                Object
                                    .keys(
                                        error.errors
                                    )
                                    .forEach(
                                        function(field) {

                                            const errorEl =
                                                document.querySelector(
                                                    '.field-error[data-field="' +
                                                    field +
                                                    '"]'
                                                );

                                            if (errorEl) {

                                                errorEl.textContent =
                                                    error.errors[
                                                        field
                                                    ][0];

                                            }

                                        }
                                    );

                                showMessage(
                                    error.message ||
                                    'Please correct the errors.',
                                    'danger'
                                );

                            } else if (
                                error.status === 403
                            ) {

                                showMessage(
                                    error.message ||
                                    'Your IP is blocked.',
                                    'danger'
                                );

                            } else {

                                showMessage(
                                    error.message ||
                                    'Something went wrong.',
                                    'danger'
                                );

                            }

                        }
                    )

                    .finally(
                        function() {

                            setLoading(false);

                        }
                    );

            }

        });
    </script>

</body>

</html>