<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use PutheaKhem\Otp\Facades\Otp;

uses(RefreshDatabase::class);

beforeEach(function () {
    Mail::fake();
});

test('can generate and store OTP', function () {
    $identifier = 'user@example.com';
    $otp = Otp::generate($identifier);

    expect($otp)->not->toBeNull()
        ->and($otp->identifier)->toBe($identifier)
        ->and($otp->otp)->toBeString();

    expect(\PutheaKhem\Otp\Models\Otp::where('identifier', $identifier)->exists())->toBeTrue();
});

test('valid OTP is verified', function () {
    $identifier = 'user@example.com';
    $otp = Otp::generate($identifier);

    $response = Otp::validate($identifier, $otp->otp);

    expect($response)->toMatchArray([
        'status' => true,
        'message' => 'OTP verified successfully.',
    ]);

    expect(\PutheaKhem\Otp\Models\Otp::where('identifier', $identifier)->where('used', true)->exists())->toBeTrue();
});

test('OTP expires correctly', function () {
    $identifier = 'user@example.com';
    $otp = Otp::generate($identifier);

    \PutheaKhem\Otp\Models\Otp::where('identifier', $identifier)->update(['expires_at' => now()->subMinutes(10)]);

    $response = Otp::validate($identifier, $otp->otp);

    expect($response)->toMatchArray([
        'status' => false,
        'message' => 'OTP expired or invalid.',
    ]);
});
