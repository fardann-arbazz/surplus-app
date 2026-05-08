<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\ResetPasswordRequest;
use App\Services\Auth\AuthService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ResetPasswordController extends Controller
{
    public function __construct(
        private AuthService $authService
    ) {}

    public function showResetForm(Request $request): View
    {
        return view('auth.reset-password', [
            'email' => $request->session()->get('email', $request->query('email', '')),
        ]);
    }

    // Verifikasi OTP dan Reset Password
    public function reset(ResetPasswordRequest $request): RedirectResponse
    {
        $user = $this->authService->verifyResetOtp(
            $request->input('email'),
            $request->input('otp')
        );

        if (!$user) {
            return back()->withErrors([
                'otp' => 'Invalid or expired OTP code.'
            ]);
        }

        $this->authService->resetPassword($user, $request->input('password'));

        // clear session email
        $request->session()->forget('email');

        return redirect()->route('login')
            ->with('status', 'Your password has been reset successfully. Please login with your new password.');
    }


    // Resend OTP untuk reset password
    public function resendOtp(Request $request)
    {
        $email = $request->session()->get('email', $request->input('email'));

        if (!$email) {
            return redirect()->route('password.forgot')
                ->withErrors(['email' => 'Please enter your email first.']);
        }

        $this->authService->sendPasswordResetOtp($email);

        return back()->with('status', 'A new OTP has been sent to your email.')
            ->with('email', $email);
    }
}   
