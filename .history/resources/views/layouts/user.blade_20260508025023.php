<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title', 'Rantangku')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @include('partials.user.styles')

    @include('partials.user.location-picker')

    @stack('styles')
</head>

<body class="@yield('body-class', 'bg-orange-50/50 font-sans antialiased')" x-data="@yield('x-data', 'homePage')">

    <div class="min-h-screen pb-20 lg:pb-0">

        {{-- Header --}}
        @include('partials.user.header')

        {{-- Main Content --}}
        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 lg:py-6 space-y-6 lg:space-y-8">
            @yield('content')
        </main>

        {{-- Cart Drawer --}}
        @include('partials.user.cart-drawer')

        {{-- Bottom Navigation --}}
        @include('partials.user.mobile-bottom-nav')
    </div>

    @include('partials.user.scripts')

    @stack('scripts')
</body>

</html>
