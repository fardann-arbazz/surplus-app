<?php

namespace App\Services\Auth;

use App\Mail\OtpVerificationMail;
use App\Mail\ResetPasswordOtpMail;
use App\Models\OtpTokens;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class OtpService
{
    private const OTP_LENGTH = 6;
    private const EXPIRY_MINUTES = 15;
    private const RESEND_COOLDOWN_MINUTES = 1;

    public function generateOtp(User $user, string $type): OtpTokens
    {
        $this->invalidateExistingOtps($user, $type);

        return OtpTokens::create([
            'user_id' => $user->id,
            'token' => $this->generateToken(),
            'type' => $type,
            'expires_at' => now()->addMinutes(self::EXPIRY_MINUTES),
        ]);
    }

    public function sendVerificationOtp(User $user): void
    {
        $otp = $this->generateOtp($user, 'email_verification');

        Mail::to($user->email)->queue(
            new OtpVerificationMail($otp->token, $user->name)
        );
    }

    public function sendPasswordResetOtp(User $user): void
    {
        $otp = $this->generateOtp($user, 'password_reset');

        Mail::to($user->email)->queue(
            new ResetPasswordOtpMail($otp->token, $user->name)
        );
    }

    public function verifyOtp(User $user, string $token, string $type): bool
    {
        $otpToken = OtpTokens::where('user_id', $user->id)
            ->where('token', $token)
            ->where('type', $type)
            ->whereNull('used_at')
            ->latest()
            ->first();

        if (!$otpToken || !$otpToken->isValid()) {
            return false;
        }

        $otpToken->markAsUsed();
        return true;
    }

    public function canResendOtp(User $user, string $type): bool
    {
        $lastOtp = OtpTokens::where('user_id', $user->id)
            ->where('type', $type)
            ->latest()
            ->first();

        if (!$lastOtp) {
            return true;
        }

        return $lastOtp->created_at->addMinutes(self::RESEND_COOLDOWN_MINUTES)->isPast();
    }

    private function generateToken(): string
    {
        return str_pad((string) random_int(0, 999999), self::OTP_LENGTH, '0', STR_PAD_LEFT);
    }

    private function invalidateExistingOtps(User $user, string $type): void
    {
        OtpTokens::where('user_id', $user->id)
            ->where('type', $type)
            ->whereNull('used_at')
            ->update(['used_at' => now()]);
    }
}
