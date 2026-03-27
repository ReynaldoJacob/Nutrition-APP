<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VerifyOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public User $user;
    public string $otp;

    public function __construct(User $user, string $otp)
    {
        $this->user = $user;
        $this->otp = $otp;
    }

    public function build(): self
    {
        return $this->subject('Verifica tu cuenta - Metabole')
            ->view('emails.verify-otp', [
                'user' => $this->user,
                'otp' => $this->otp,
            ])
            ->text('emails.verify-otp-text', [
                'user' => $this->user,
                'otp' => $this->otp,
            ]);
    }
}
