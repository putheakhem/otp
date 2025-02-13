<!DOCTYPE html>
<html>
<head>
    <title>OTP Verification</title>
</head>
<body>
<p>Your OTP code is: <strong>{{ $otp }}</strong></p>
<p>This OTP will expire in {{ config('otp.expires_in') / 60 }} minutes.</p>
</body>
</html>
