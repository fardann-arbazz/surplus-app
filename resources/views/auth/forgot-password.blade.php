<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Forgot Password - {{ config('app.name') }}</title>
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
                                    d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                            </svg>
                        </div>
                        <h1 class="text-xl font-semibold text-slate-800">Forgot Password?</h1>
                        <p class="text-sm text-slate-500 mt-1">Enter your email and we'll send you an OTP</p>
                    </div>

                    <!-- Session Status -->
                    @if (session('status'))
                        <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded-xl">
                            <p class="text-sm text-green-700">{{ session('status') }}</p>
                        </div>
                    @endif

                    <!-- Forgot Password Form -->
                    <form method="POST" action="{{ route('password.send-otp') }}" class="space-y-4">
                        @csrf

                        <div>
                            <label for="email" class="block text-sm font-medium text-slate-700 mb-1.5">
                                Email address
                            </label>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" required
                                autofocus
                                class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 transition-all"
                                placeholder="johndoe@email.com">
                            @error('email')
                                <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <button type="submit"
                            class="w-full bg-orange-500 text-white py-3 rounded-2xl font-semibold text-sm hover:bg-orange-600 focus:outline-none focus:ring-2 focus:ring-orange-500/50 focus:ring-offset-2 transition-all duration-200 active:scale-[0.98]">
                            Send OTP
                        </button>
                    </form>

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
