<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verify Email - {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-orange-50">
    <div class="min-h-screen flex flex-col">
        <div class="flex-1 flex items-start justify-center px-4 mt-52">
            <div class="w-full max-w-sm sm:max-w-md">
                <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-6">

                    <!-- Header -->
                    <div class="text-center mb-6">
                        <div class="mx-auto w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mb-4">
                            <svg class="w-8 h-8 text-orange-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <h1 class="text-xl font-semibold text-slate-800">Verify Your Email</h1>
                        <p class="text-sm text-slate-500 mt-1">We've sent a 6-digit OTP to your email</p>
                    </div>

                    <!-- Session Status -->
                    @if (session('status'))
                        <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded-xl">
                            <p class="text-sm text-green-700">{{ session('status') }}</p>
                        </div>
                    @endif

                    <!-- OTP Form -->
                    <form method="POST" action="{{ route('otp.verify') }}" class="space-y-4">
                        @csrf

                        <div>
                            <label for="otp" class="block text-sm font-medium text-slate-700 mb-1.5">
                                Enter OTP Code
                            </label>
                            <input id="otp" type="text" name="otp" required autofocus maxlength="6"
                                inputmode="numeric" pattern="[0-9]{6}"
                                class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-sm text-slate-800 text-center tracking-[0.5em] font-mono text-lg placeholder-slate-300 focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 transition-all"
                                placeholder="000000">
                            @error('otp')
                                <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <button type="submit"
                            class="w-full bg-orange-500 text-white py-3 rounded-2xl font-semibold text-sm hover:bg-orange-600 focus:outline-none focus:ring-2 focus:ring-orange-500/50 focus:ring-offset-2 transition-all duration-200 active:scale-[0.98]">
                            Verify Email
                        </button>
                    </form>

                    <!-- Resend OTP -->
                    <div class="mt-4 text-center">
                        <p class="text-sm text-slate-500">
                            Didn't receive the code?
                        <form method="POST" action="{{ route('otp.resend') }}" class="inline">
                            @csrf
                            <button type="submit" class="text-orange-600 hover:text-orange-700 font-medium ml-1">
                                Resend OTP
                            </button>
                        </form>
                        </p>
                    </div>

                    <!-- Back to Login -->
                    <div class="mt-4 text-center">
                        <a href="{{ route('login') }}" class="text-sm text-slate-500 hover:text-slate-700">
                            ← Back
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
