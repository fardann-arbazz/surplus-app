<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reset Password - {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-orange-50">
    <div class="min-h-screen flex flex-col">
        <div class="flex-1 flex items-start justify-center px-4 mt-36">
            <div class="w-full max-w-sm sm:max-w-md">
                <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-6">

                    <!-- Header -->
                    <div class="text-center mb-6">
                        <div class="mx-auto w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mb-4">
                            <svg class="w-8 h-8 text-orange-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <h1 class="text-xl font-semibold text-slate-800">Reset Password</h1>
                        <p class="text-sm text-slate-500 mt-1">Enter OTP and your new password</p>
                    </div>

                    <!-- Session Status -->
                    @if (session('status'))
                        <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded-xl">
                            <p class="text-sm text-green-700">{{ session('status') }}</p>
                        </div>
                    @endif

                    <!-- Reset Password Form -->
                    <form method="POST" action="{{ route('password.reset.submit') }}" class="space-y-4">
                        @csrf

                        <!-- Hidden Email -->
                        <input type="hidden" name="email" value="{{ $email ?? old('email') }}">

                        <!-- OTP -->
                        <div>
                            <label for="otp" class="block text-sm font-medium text-slate-700 mb-1.5">
                                OTP Code
                            </label>
                            <input id="otp" type="text" name="otp" required autofocus maxlength="6"
                                inputmode="numeric" pattern="[0-9]{6}"
                                class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-sm text-slate-800 text-center tracking-[0.5em] font-mono placeholder-slate-300 focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 transition-all"
                                placeholder="000000">
                            @error('otp')
                                <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- New Password -->
                        <div>
                            <label for="password" class="block text-sm font-medium text-slate-700 mb-1.5">
                                New Password
                            </label>
                            <input id="password" type="password" name="password" required
                                class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 transition-all"
                                placeholder="Min. 8 characters">
                            @error('password')
                                <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Confirm Password -->
                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-slate-700 mb-1.5">
                                Confirm New Password
                            </label>
                            <input id="password_confirmation" type="password" name="password_confirmation" required
                                class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 transition-all"
                                placeholder="Re-enter password">
                            @error('password_confirmation')
                                <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <button type="submit"
                            class="w-full bg-orange-500 text-white py-3 rounded-2xl font-semibold text-sm hover:bg-orange-600 focus:outline-none focus:ring-2 focus:ring-orange-500/50 focus:ring-offset-2 transition-all duration-200 active:scale-[0.98]">
                            Reset Password
                        </button>
                    </form>

                    <!-- Resend OTP -->
                    <div class="mt-4 text-center">
                        <p class="text-sm text-slate-500">
                            Didn't receive the code?
                        <form method="POST" action="{{ route('password.reset.resend') }}" class="inline">
                            @csrf
                            <input type="hidden" name="email" value="{{ $email ?? old('email') }}">
                            <button type="submit" class="text-orange-600 hover:text-orange-700 font-medium ml-1">
                                Resend OTP
                            </button>
                        </form>
                        </p>
                    </div>

                    <!-- Back to Login -->
                    <div class="mt-4 text-center">
                        <a href="{{ route('login') }}" class="text-sm text-slate-500 hover:text-slate-700">
                            ← Back to Login
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
