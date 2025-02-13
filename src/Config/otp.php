<?php

return [
    'length' => 6, // OTP length
    'expires_in' => 300, // OTP expiration time in seconds (5 min)
    'max_attempts' => 5, // Allowed failed attempts before invalidation
    'lock_to_session' => true, // OTP tied to a user's session
    'mail_template' => 'otp::emails.otp', // Email template for OTP
    'logging_enabled' => true, // Enable OTP logging
];
