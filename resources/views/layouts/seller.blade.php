<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Seller Dashboard') - Rantangku</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

    @stack('head')
</head>

<body class="bg-slate-50 font-sans antialiased" x-data="sellerDashboard">

    <div class="min-h-screen flex">

        {{-- Overlay mobile --}}
        <div x-show="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 bg-black/50 z-40 lg:hidden"
            x-transition.opacity>
        </div>

        {{-- Sidebar --}}
        @include('partials.seller.sidebar')

        {{-- Main --}}
        <main class="flex-1 min-w-0">

            {{-- Header --}}
            @include('partials.seller.header')

            {{-- Konten halaman --}}
            <div class="p-4 lg:p-6 space-y-6">
                @yield('content')
            </div>

        </main>

    </div>

    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>

    @stack('scripts')

</body>

</html>
