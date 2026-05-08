<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\ForgotPasswordRequest;
use App\Services\Auth\AuthService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ForgotPasswordController extends Controller
{
    public function __construct(
        private AuthService $authService
    ) {}

    public function showForgotForm(): View
    {
        return view('auth.forgot-password');
    }

    // Kirim otp reset password ke email
    public function sendResetOtp(ForgotPasswordRequest $request): RedirectResponse
    {
        $this->authService->sendPasswordResetOtp($request->input('email'));

        return redirect()->route('password.reset')
            ->with('status', 'If the email is registered, we have sent a password reset OTP.')
            ->with('email', $request->input('email'));
    }
}
