<?php

use Illuminate\Support\Facades\Mail;
use PutheaKhem\Otp\Mail\OtpMail;
use PutheaKhem\Otp\Models\Otp;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('API can generate OTP', function (): void {
    Mail::fake();

    $response = $this->postJson('/api/otp/generate', ['identifier' => 'user@example.com']);

    $response->assertStatus(200)
        ->assertJsonStructure(['message', 'expires_at']);

    Mail::assertSent(OtpMail::class);
    expect(Otp::where('identifier', 'user@example.com')->exists())->toBeTrue();
});
