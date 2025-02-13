<?php

namespace PutheaKhem\Otp\Mail;

use Illuminate\Mail\Mailable;

class OtpMail extends Mailable
{
    public function __construct(public $otp) {}

    public function build()
    {
        return $this->subject('Your OTP Code')
            ->view('otp::emails.otp')
            ->with(['otp' => $this->otp]);
    }
}
