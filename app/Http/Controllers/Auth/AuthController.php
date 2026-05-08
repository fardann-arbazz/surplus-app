<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\VerifyOtpRequest;
use App\Services\Auth\AuthService;
use App\Services\Auth\LoginAttemptService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function __construct(
        private AuthService $authService,
        private LoginAttemptService $loginAttemptService
    ) {}

    public function index(): View
    {
        return view('auth.login');
    }

    public function login(LoginRequest $request): RedirectResponse
    {
        // check rate limitting
        if ($this->loginAttemptService->tooManyAttempts($request)) {
            $seconds = $this->loginAttemptService->availableIn($request);
            $minutes = ceil($seconds / 60);

            throw ValidationException::withMessages([
                'email' => ["Too many login attempts. Please try again in {$minutes} minutes"]
            ]);
        }

        try {
            $user = $this->authService->login(
                $request->only('email', 'password'),
                $request->boolean('remember')
            );

            $this->loginAttemptService->clearAttempts($request);

            $request->session()->regenerate();

            return redirect()->intended($user->getRedirectRoute());
        } catch (ValidationException $e) {
            $this->loginAttemptService->incrementAttempts($request);

            $remaining = $this->loginAttemptService->remainingAttempts($request);

            if ($remaining > 0) {
                throw ValidationException::withMessages([
                    'email' => ["Invalid credentials. {$remaining} attempts remaining."],
                ]);
            }

            throw $e;
        }
    }

    public function register()
    {
        return view('auth.register');
    }


    // Proses register
    public function store(RegisterRequest $request): RedirectResponse
    {
        $this->authService->register($request->validated());

        $request->session()->regenerate();

        return redirect()->route('otp.verify-email')->with('status', 'Registration successful! Please check your email for the OTP verification code.');
    }

    // Tampil halaman verifikasi OTP 
    public function showOtpVerification(): View
    {
        return view('auth.otp-verification');
    }

    // Proses verifikasi email dengan OTP
    public function verifyOtp(VerifyOtpRequest $request)
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        // jika email sudah terverifikasi, redirect langsung
        if ($user->hasVerifiedEmail()) {
            return redirect($user->getRedirectRoute())->with('status', 'Your email is already verified.');
        }

        $verified = $this->authService->verifyEmail($user, $request->input('otp'));

        if (!$verified) {
            return back()->withErrors([
                'otp' => 'Invalid or expired OTP code. Please request a new one.',
            ]);
        }

        return redirect($user->getRedirectRoute())
            ->with('status', 'Email verified successfully! Welcome to ' . config('app.name') . '.');
    }

    // Kirim ulang OTP verifikasi email
    public function resendOtp(): RedirectResponse
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        if ($user->hasVerifiedEmail()) {
            return redirect($user->getRedirectRoute())
                ->with('status', 'Your email is already verified.');
        }

        $this->authService->resendVerificationOtp($user);

        return back()->with('status', 'A new OTP code has been sent to your email.');
    }

    // Logout user
    public function logout(Request $request): RedirectResponse
    {
        $this->authService->logout();

        return redirect()->route('login')
            ->with('status', 'You have been logged out successfully.');
    }
}
