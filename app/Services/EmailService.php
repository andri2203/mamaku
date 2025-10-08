<?php

namespace App\Services;

use App\Mail\VerificationCodeMail;
use Illuminate\Support\Facades\Mail;

class EmailService
{
    public function sendVerificationCode(string $email): string
    {
        $code = rand(100000, 999999); // 6 digit acak

        Mail::to($email)->send(new VerificationCodeMail($code));

        // simpan ke session (sementara, bisa ubah ke DB nanti)
        session([
            'verification_code' => $code,
            'verification_email' => $email,
            'verification_expires_at' => now()->addMinutes(10),
        ]);

        return $code;
    }
}
