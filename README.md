# PHP_Laravel12_Google_ReCaptcha_Implement

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel">
  <img src="https://img.shields.io/badge/Google-reCAPTCHA%20v2-blue?style=for-the-badge&logo=google">
  <img src="https://img.shields.io/badge/Bot%20Protection-Enabled-success?style=for-the-badge">
</p>

---

##  Overview  
Google reCAPTCHA v2 protects your Laravel forms from **spam**, **bots**, and **automated submissions**.

This documentation includes:

✔ Installing Laravel  
✔ Creating Google API keys  
✔ Adding `.env` configuration  
✔ Custom validation rule  
✔ Controller validation  
✔ Blade form with reCAPTCHA widget  

---

##  Features  

###  Security Features  
- Protects forms from bots & automated tools  
- Validates user’s browser token via Google API  
- Prevents spam submissions  
- Ensures only humans can submit the form  

###  Technical Features  
- Custom Laravel Validation Rule (`ReCaptcha`)  
- Secure server-side verification  
- API communication via Laravel HTTP Client  
- Supports Laravel 10–12  

###  UI Features  
- Classic Google **“I’m not a robot”** checkbox  
- Error messages visible under widget  
- Fully responsive  
- Bootstrap design  

###  Developer-Friendly  
- Only 1 file to integrate  
- Clean controller + rule separation  
- Works on any form (login, contact, register, comments)  
- Can be upgraded easily to reCAPTCHA v3 or Invisible  

---

#  Folder Structure  
```
app/
├── Http/
│   └── Controllers/
│       └── ContactController.php
├── Rules/
│   └── ReCaptcha.php
resources/
└── views/
    └── contactForm.blade.php

routes/
└── web.php
.env
README.md
```

---

#  Step 1 — Install Laravel  
```bash
composer create-project laravel/laravel example-app
```

---

#  Step 2 — Setup Database (.env)
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=root
DB_PASSWORD=
```

---

#  Step 3 — Create Google reCAPTCHA v2 Keys  

Go to:

 https://www.google.com/recaptcha/admin

Choose:

✔ reCAPTCHA v2  
✔ “I’m not a robot” Checkbox  
✔ Add domain:  
```
localhost
127.0.0.1
```
<img width="1036" height="918" alt="Screenshot 2025-12-11 151320" src="https://github.com/user-attachments/assets/1e6a16b2-c32a-4852-82c0-0b3bf8a95f29" />

<img width="1006" height="672" alt="Screenshot 2025-12-11 151401" src="https://github.com/user-attachments/assets/48f977e0-30e5-4d62-9be5-3a6091edce58" />


Get:

- **SITE KEY**  
- **SECRET KEY**

---

#  Step 4 — Add Keys in .env  
```
GOOGLE_RECAPTCHA_KEY=your_site_key_here
GOOGLE_RECAPTCHA_SECRET=your_secret_key_here
```

---

#  Step 5 — Create Routes  

```php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContactController;

Route::get('contact-us', [ContactController::class, 'index']);
Route::post('contact-us', [ContactController::class, 'store'])->name('contact.us.store');
```

---

#  Step 6 — Create Custom Validation Rule  

```bash
php artisan make:rule ReCaptcha
```

```php
<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Http;

class ReCaptcha implements Rule
{
    public function passes($attribute, $value)
    {
        $response = Http::get("https://www.google.com/recaptcha/api/siteverify", [
            'secret'   => env('GOOGLE_RECAPTCHA_SECRET'),
            'response' => $value,
        ]);

        return $response->json()["success"];
    }

    public function message()
    {
        return 'Google reCAPTCHA verification failed.';
    }
}
```

---

#  Step 7 — Create Controller  

```php
<?php 

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Rules\ReCaptcha;

class ContactController extends Controller
{
    public function index()
    {
        return view('contactForm');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'                  => 'required',
            'email'                 => 'required|email',
            'phone'                 => 'required|digits:10|numeric',
            'subject'               => 'required',
            'message'               => 'required',
            'g-recaptcha-response'  => ['required', new ReCaptcha],
        ]);

        dd($request->all());
    }
}
```

---

#  Step 8 — Create Blade Form  

```blade
<!DOCTYPE html>
<html>
<head>
    <title>Laravel Google ReCaptcha V2 Example</title>

    <link rel="stylesheet" 
          href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/css/bootstrap.min.css" />

    <script src="https://www.google.com/recaptcha/api.js"></script>
</head>

<body>
    <div class="container mt-5">
        <div class="col-10 offset-1">

            <div class="card">
                <div class="card-header bg-primary">
                    <h3 class="text-white">Laravel Google reCAPTCHA V2 Example</h3>
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route('contact.us.store') }}">
                        @csrf

                        <div class="mt-3">
                            <div class="g-recaptcha" 
                                 data-sitekey="{{ env('GOOGLE_RECAPTCHA_KEY') }}">
                            </div>
                            @error('g-recaptcha-response') 
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="text-center mt-4">
                            <button class="btn btn-success">Submit</button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
</body>
</html>
```

---

#  Step 9 — Run App  
```bash
php artisan serve
```

Visit:

```
http://127.0.0.1:8000/contact-us
```
<img width="961" height="618" alt="Screenshot 2025-12-11 151102" src="https://github.com/user-attachments/assets/8edefe92-f3f9-43e8-843c-1f20fb6901c8" />

<img width="1394" height="177" alt="Screenshot 2025-12-11 150554" src="https://github.com/user-attachments/assets/9cf8ea48-584d-4ef0-b023-a79c9efcbe15" />


---

#  SUCCESS! reCAPTCHA is Working  

✔ Human verification works  
✔ Backend validation is secure  
✔ Bots cannot submit the form  
✔ Form data appears via `dd()`  

---
