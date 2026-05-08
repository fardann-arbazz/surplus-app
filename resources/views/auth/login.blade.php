<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'Login Page') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

    <!-- Styles / Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

</head>

<body class="min-h-screen bg-orange-50">

    <!-- Main Container -->
    <div class="min-h-screen flex flex-col">

        <!-- Login Card -->
        <div class="flex-1 flex items-start justify-center px-4 mt-52">
            <div class="w-full max-w-sm sm:max-w-lg">

                <!-- Card -->
                <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-6">

                    <!-- Header -->
                    <div class="text-center mb-6">
                        <h1 class="text-xl font-semibold text-slate-800">Welcome back!</h1>
                        <p class="text-sm text-slate-500 mt-1">Log in to order your favorite food</p>
                    </div>

                    <!-- Session Status -->
                    @if (session('status'))
                        <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded-xl">
                            <p class="text-sm text-green-700">{{ session('status') }}</p>
                        </div>
                    @endif

                    <!-- Form -->
                    <form method="POST" action="{{ route('login.submit') }}" class="space-y-4">
                        @csrf

                        <!-- Phone/Email -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-slate-700 mb-1.5">
                                Phone number or email
                            </label>
                            <input id="email" type="text" name="email" value="{{ old('email') }}" required
                                autofocus autocomplete="username"
                                class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 transition-all"
                                placeholder="0812 3456 7890">
                            @error('email')
                                <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Password -->
                        <div>
                            <label for="password" class="block text-sm font-medium text-slate-700 mb-1.5">
                                Password
                            </label>
                            <input id="password" type="password" name="password" required
                                autocomplete="current-password"
                                class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 transition-all"
                                placeholder="Enter password">
                            @error('password')
                                <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Forgot Password -->
                        <div class="text-right">
                            @if (Route::has('password.forgot'))
                                <a href="{{ route('password.forgot') }}"
                                    class="text-sm text-orange-600 hover:text-orange-700 font-medium">
                                    Forgot password?
                                </a>
                            @endif
                        </div>

                        <!-- Login Button -->
                        <button type="submit"
                            class="w-full bg-orange-500 text-white py-3 rounded-2xl font-semibold text-sm hover:bg-orange-600 focus:outline-none focus:ring-2 focus:ring-orange-500/50 focus:ring-offset-2 transition-all duration-200 active:scale-[0.98]">
                            Log in
                        </button>
                    </form>

                    <!-- Divider -->
                    <div class="my-6 flex items-center gap-3">
                        <div class="flex-1 h-px bg-slate-200"></div>
                        <span class="text-xs text-slate-400 font-medium">or</span>
                        <div class="flex-1 h-px bg-slate-200"></div>
                    </div>

                    <!-- Social Login -->
                    <div class="space-y-2.5">
                        <button type="button"
                            class="w-full py-2.5 border border-slate-200 rounded-2xl text-sm font-medium text-slate-700 hover:bg-slate-50 transition-all flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" viewBox="0 0 24 24">
                                <path fill="#4285F4"
                                    d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 0 1-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z" />
                                <path fill="#34A853"
                                    d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" />
                                <path fill="#FBBC05"
                                    d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" />
                                <path fill="#EA4335"
                                    d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" />
                            </svg>
                            Google
                        </button>

                        <button type="button"
                            class="w-full py-2.5 border border-slate-200 rounded-2xl text-sm font-medium text-slate-700 hover:bg-slate-50 transition-all flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none">
                                <path
                                    d="M17.05 20.28c-.98.95-2.05.88-3.08.4-1.09-.5-2.08-.48-3.24 0-1.44.62-2.2.44-3.06-.4C2.79 15.25 3.51 7.59 9.05 7.31c1.35.07 2.29.74 3.08.8 1.18-.24 2.31-.93 3.57-.84 1.51.12 2.65.72 3.4 1.8-3.12 1.87-2.38 5.98.48 7.13-.57 1.5-1.31 2.99-2.54 4.09z"
                                    fill="#000" />
                            </svg>
                            Apple
                        </button>
                    </div>

                    <!-- Register -->
                    <p class="mt-6 text-center text-sm text-slate-500">
                        Don't have an account?
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}"
                                class="text-orange-600 hover:text-orange-700 font-semibold ml-1">
                                Sign up
                            </a>
                        @endif
                    </p>
                </div>

                <!-- Footer -->
                <p class="mt-6 text-center text-xs text-slate-400">
                    By continuing, you agree to our
                    <a href="#" class="underline">Terms</a> &
                    <a href="#" class="underline">Privacy</a>
                </p>
            </div>
        </div>
    </div>

</body>

</html>
