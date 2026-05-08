<?php

namespace App\Services\Auth;

use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService
{
    public function __construct(
        private OtpService $otpService,
        private LoginAttemptService $loginAttemptService,
    ) {}

    public function register(array $data): User
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'password' => $data['password'],
            'role' => 'user',
        ]);

        // Kirim OTP verifikasi email
        $this->otpService->sendVerificationOtp($user);

        event(new Registered($user));

        // Login user setelah register
        Auth::login($user);

        return $user;
    }

    public function login(array $credentials, bool $remember = false): User
    {
        $user = User::where('email', $credentials['email'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        Auth::login($user, $remember);

        return $user;
    }

    public function verifyEmail(User $user, string $otp): bool
    {
        if ($this->otpService->verifyOtp($user, $otp, 'email_verification')) {
            $user->markEmailAsVerified();
            return true;
        }

        return false;
    }

    public function resendVerificationOtp(User $user): void
    {
        if (!method_exists($user, 'hasVerifiedEmail') || !$user->hasVerifiedEmail()) {
            $this->otpService->sendVerificationOtp($user);
        }
    }

    public function sendPasswordResetOtp(string $email): void
    {
        $user = User::where('email', $email)->first();

        if ($user) {
            $this->otpService->sendPasswordResetOtp($user);
        }
    }

    public function verifyResetOtp(string $email, string $otp): ?User
    {
        $user = User::where('email', $email)->first();

        if (!$user) {
            return null;
        }

        if ($this->otpService->verifyOtp($user, $otp, 'password_reset')) {
            return $user;
        }

        return null;
    }

    public function resetPassword(User $user, string $password): void
    {
        $user->update([
            'password' => $password,
        ]);

        event(new PasswordReset($user));
    }

    public function logout(): void
    {
        Auth::logout();

        request()->session()->invalidate();
        request()->session()->regenerateToken();
    }
}
