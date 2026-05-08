<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title', 'Admin Dashboard') - Rantangku</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

    <!-- Styles / Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

    @stack('styles')
</head>

<body class="bg-base-200/50 font-sans antialiased" x-data="{ sidebarOpen: false }">

    <div class="min-h-screen flex">

        <!-- Sidebar Overlay (Mobile) -->
        <div x-show="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 bg-black/50 z-40 lg:hidden"
            x-transition.opacity>
        </div>

        <!-- Sidebar -->
        @include('partials.admin.sidebar')

        <!-- Main Content -->
        <main class="flex-1 min-w-0">

            <!-- Top Bar -->
            @include('partials.admin.topbar')

            <!-- Page Content -->
            <div class="p-4 lg:p-6 space-y-6">
                @yield('content')
            </div>

        </main>
    </div>

    @stack('scripts')

    <style>
        [x-cloak] {
            display: none !important;
        }

        .scrollbar-thin::-webkit-scrollbar {
            width: 4px;
        }

        .scrollbar-thin::-webkit-scrollbar-track {
            background: transparent;
        }

        .scrollbar-thin::-webkit-scrollbar-thumb {
            background: #475569;
            border-radius: 4px;
        }
    </style>

</body>

</html>
