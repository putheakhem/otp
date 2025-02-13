<?php

namespace PutheaKhem\Otp\Services;

use Illuminate\Support\Facades\Mail;
use PutheaKhem\Otp\Mail\OtpMail;
use PutheaKhem\Otp\Models\Otp;

class OtpService
{
    /**
     * Generate a new OTP for a given identifier.
     */
    public function generate(string $identifier): Otp
    {
        $otp = (string) random_int(100000, 999999);
        $expiresAt = now()->addSeconds(config('otp.expires_in'));

        // Delete previous OTPs for this identifier
        Otp::where('identifier', $identifier)->delete();

        // Create new OTP
        /** @var Otp $otpRecord */
        $otpRecord = Otp::create([
            'identifier' => $identifier,
            'otp' => $otp,
            'session_id' => session()->getId() ?? '',
            'expires_at' => $expiresAt,
        ]);

        // Send OTP via mail
        Mail::to($identifier)->send(new OtpMail($otp));

        return $otpRecord;
    }

    /**
     * Validate the provided OTP.
     *
     * @return array<string, mixed>
     */
    public function validate(string $identifier, string $otp): array
    {
        /** @var Otp|null $otpRecord */
        $otpRecord = Otp::where('identifier', $identifier)
            ->where('used', false)
            ->where('expires_at', '>', now())
            ->first();

        if ($otpRecord === null) {
            return ['status' => false, 'message' => 'OTP expired or invalid.'];
        }

        // Ensure session lock validation
        if (config('otp.lock_to_session') && $otpRecord->session_id !== (session()->getId() ?? '')) {
            return ['status' => false, 'message' => 'OTP is locked to a different session.'];
        }

        // Check maximum attempts
        if ($otpRecord->attempts >= (int) config('otp.max_attempts')) {
            $otpRecord->delete();

            return ['status' => false, 'message' => 'Too many failed attempts. OTP invalidated.'];
        }

        // Check if OTP matches
        if ($otpRecord->otp === $otp) {
            $otpRecord->update(['used' => true]);

            return ['status' => true, 'message' => 'OTP verified successfully.'];
        }

        $otpRecord->increment('attempts');

        return ['status' => false, 'message' => 'Invalid OTP.'];
    }
}
