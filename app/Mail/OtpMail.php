<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class OtpMail extends Mailable
{
    public string $otp;

    public function __construct(string $otp)
    {
        $this->otp = $otp;
    }

    public function build(): OtpMail
    {
        return $this->subject('Your OTP Code')
            ->view('emails.otp')
            ->with(['otp' => $this->otp]);
    }
}
