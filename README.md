# Laravel OTP Package - `putheakhem/otp`

[![Tests](https://github.com/putheakhem/otp/actions/workflows/tests.yml/badge.svg)](https://github.com/putheakhem/otp/actions/workflows/tests.yml)

A Laravel package for generating, validating, and managing One-Time Passwords (OTP) with security features.

---

## 📌 Features
- ✅ Rate-limited OTP generation
- ✅ Configurable expiration times
- ✅ Invalidate OTP after first use
- ✅ Lock OTP to user session
- ✅ Invalidate OTP after too many failed attempts
- ✅ View detailed error messages
- ✅ Customizable mail template
- ✅ Auditable logs for security

---

## 🔧 Installation

### 1️⃣ Install via Composer
```sh
composer require putheakhem/otp
```

### 2️⃣ Publish Configuration & Migrations
```sh
php artisan vendor:publish --provider="PutheaKhem\Otp\Providers\OtpServiceProvider"
php artisan migrate
```

This will create:
- A **config file** at `config/otp.php`
- A **database table** `otps`

---

### **3 Configuration**

Modify `config/otp.php` to adjust settings:

```php
return [
    'length' => 6, // OTP length
    'expires_in' => 300, // OTP expiration time in seconds (5 minutes)
    'max_attempts' => 5, // Maximum failed attempts before invalidation
    'lock_to_session' => true, // OTP tied to user session
    'mail_template' => 'otp::emails.otp', // Email template for OTP
    'logging_enabled' => true, // Enable OTP logging
];
```

---

## 🔥 Usage

### **Generate an OTP**
```php
use PutheaKhem\Otp\Facades\Otp;

$otp = Otp::generate('user@example.com');

dd($otp);
```

📌 **Output Example:**
```php
PutheaKhem\Otp\Models\Otp {#123
  id: 1,
  identifier: "user@example.com",
  otp: "123456",
  used: false,
  attempts: 0,
  expires_at: "2025-02-10 12:00:00"
}
```

---

### **Validate an OTP**
```php
$response = Otp::validate('user@example.com', '123456');

dd($response);
```

📌 **Expected Output:**
✅ **Success**
```json
{
    "status": true,
    "message": "OTP verified successfully."
}
```

❌ **Failure (Invalid OTP)**
```json
{
    "status": false,
    "message": "Invalid OTP."
}
```

❌ **Failure (Expired OTP)**
```json
{
    "status": false,
    "message": "OTP expired or invalid."
}
```

---

## 📩 Email Customization
Customize the email template at:
```
resources/views/vendor/otp/emails/otp.blade.php
```

Example:

```blade
<!DOCTYPE html>
<html>
<head>
    <title>OTP Verification</title>
</head>
<body>
    <p>Your OTP is: <strong>{{ $otp }}</strong></p>
    <p>This OTP is valid for {{ config('otp.expires_in') / 60 }} minutes.</p>
</body>
</html>
```

---

## 🔬 Testing

### **Run All Tests**
```sh
php artisan test
```

📌 **Expected Output:**
```
✔ can generate OTP
✔ can validate OTP
✔ OTP invalid after expiry
✔ OTP fails after too many attempts
✔ OTP logs events
✔ Emails are sent correctly
✔ OTP is locked to session
```

---

## 📢 Contributing
1. Fork the repository
2. Clone the repo:
   ```sh
   git clone https://github.com/putheakhem/otp.git
   ```
3. Create a new branch:
   ```sh
   git checkout -b feature-branch
   ```
4. Commit changes & push:
   ```sh
   git commit -m "Added new feature"
   git push origin feature-branch
   ```
5. Submit a **Pull Request** 🚀

---

## 🏆 Credits
Developed by **[Puthea Khem](https://github.com/putheakhem)**.  
Special thanks to the **Laravel community**! 🎉

---

## 📜 License
This package is open-source and licensed under the **MIT License**.

