# 🚀 Laravel OTP Package - `putheakhem/otp`

[![Tests](https://github.com/putheakhem/otp/actions/workflows/tests.yml/badge.svg)](https://github.com/putheakhem/otp/actions/workflows/tests.yml)

A Laravel package for generating, validating, and managing One-Time Passwords (OTP) with security features.
## 📌 Features
✅ **Rate-limited OTP generation**  
✅ **Configurable expiration times**  
✅ **Invalidate OTP after first use**  
✅ **Lock OTP to user session**  
✅ **Invalidate OTP after too many failed attempts**  
✅ **View detailed error messages**  
✅ **Customizable mail template**  
✅ **Auditable logs for security**

---

## 🔧 Installation

### **1. Install via Composer**
```bash
composer require putheakhem/otp
```

### **2. Publish the configuration file**
```bash

php artisan vendor:publish --provider="Putheakhem\Otp\OtpServiceProvider" --tag="config"
```
this will publish the `otp.php` configuration file to your `config` directory.

### **3. Run the migrations**
```bash

php artisan migrate
```

### **4. Configuration** 
Modify `config/otp.php` to adjust settings:

```shell
return [
    'length' => 6, // OTP length
    'expires_in' => 300, // OTP expiration time in seconds (5 minutes)
    'max_attempts' => 5, // Maximum failed attempts before invalidation
    'lock_to_session' => true, // OTP tied to user session
    'mail_template' => 'otp::emails.otp', // Email template for OTP
    'logging_enabled' => true, // Enable OTP logging
];
```

## 🚀 Usage

### **1. Generate an OTP**
```php
use Putheakhem\Otp\Facades\Otp;

$otp = Otp::generate('user@gmail.com');

dd($otp);
``` 
output:
```shell
PutheaKhem\Otp\Models\Otp {#123
  id: 1,
  identifier: "user@example.com",
  otp: "123456",
  used: false,
  attempts: 0,
  expires_at: "2025-02-10 12:00:00"
}
```

### **2. Validate an OTP**

```php
use Putheakhem\Otp\Facades\Otp;

$response = Otp::validate('user@example.com', '123456');

dd($response);
```

✅ Expected Output: Success
```json
{
    "status": true,
    "message": "OTP verified successfully."
}
```

❌ Failure (Invalid OTP)
```json
{
    "status": false,
    "message": "Invalid OTP."
}
```

❌ Failure (Expired OTP)
```json
{
    "status": false,
    "message": "OTP expired or invalid."
}
```

### **3. Email Customization**

Customize the email template at
```shell
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