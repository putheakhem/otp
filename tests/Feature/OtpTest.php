<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use PutheaKhem\Otp\Facades\Otp;
use PutheaKhem\Otp\Mail\OtpMail;

uses(RefreshDatabase::class);

beforeEach(function () {
    Mail::fake();
    Log::spy();
});

test('can generate OTP', function () {
    $identifier = 'user@example.com';

    $otp = Otp::generate($identifier);

    expect($otp)->not->toBeNull();
    expect($otp->identifier)->toBe($identifier);
    expect($otp->otp)->toBeString();
    expect($otp->expires_at)->toBeInstanceOf(\Carbon\Carbon::class);

    Mail::assertSent(OtpMail::class, function ($mail) use ($identifier, $otp) {
        return $mail->hasTo($identifier) && $mail->otp === $otp->otp;
    });
});

test('can validate a correct OTP', function () {
    $identifier = 'user@example.com';
    $otp = Otp::generate($identifier);

    $response = Otp::validate($identifier, $otp->otp);

    expect($response)->toMatchArray([
        'status' => true,
        'message' => 'OTP verified successfully.',
    ]);

    expect(Otp::where('identifier', $identifier)->where('used', true)->exists())->toBeTrue();
});

test('OTP invalid after expiry', function () {
    $identifier = 'user@example.com';
    $otp = Otp::generate($identifier);

    // Expire the OTP
    Otp::where('identifier', $identifier)->update(['expires_at' => now()->subMinutes(10)]);

    $response = Otp::validate($identifier, $otp->otp);

    expect($response)->toMatchArray([
        'status' => false,
        'message' => 'OTP expired or invalid.',
    ]);
});

test('OTP fails after too many attempts', function () {
    $identifier = 'user@example.com';
    Otp::generate($identifier);

    for ($i = 0; $i < config('otp.max_attempts'); $i++) {
        Otp::validate($identifier, '999999');
    }

    $response = Otp::validate($identifier, '999999');

    expect($response)->toMatchArray([
        'status' => false,
        'message' => 'Too many failed attempts. OTP invalidated.',
    ]);
});

test('logs OTP events', function () {
    $identifier = 'user@example.com';

    Otp::generate($identifier);

    Log::shouldHaveReceived('info')->with("OTP generated for user: {$identifier}");
});

test('emails are sent correctly', function () {
    $identifier = 'user@example.com';

    Otp::generate($identifier);

    Mail::assertSent(OtpMail::class, function ($mail) use ($identifier) {
        return $mail->hasTo($identifier);
    });
});

test('OTP is locked to session', function () {
    $identifier = 'user@example.com';
    $otp = Otp::generate($identifier);

    // Simulate different session
    session()->put('laravel_session', 'new-session-id');

    $response = Otp::validate($identifier, $otp->otp);

    expect($response)->toMatchArray([
        'status' => false,
        'message' => 'OTP is locked to a different session.',
    ]);
});
