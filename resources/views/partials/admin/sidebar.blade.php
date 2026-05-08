@php $active = $activeMenu ?? 'dashboard'; @endphp

<aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
    class="fixed lg:static lg:translate-x-0 inset-y-0 left-0 w-72 bg-slate-900 z-50 transition-transform duration-300 flex flex-col overflow-hidden">

    <!-- Sidebar Header -->
    <div class="p-5 border-b border-slate-700/50">
        <div class="flex items-center gap-3">
            <div>
                <span class="text-lg font-bold text-white">Rantangku</span>
                <p class="text-xs text-slate-400">Admin Panel</p>
            </div>
        </div>
    </div>

    <!-- Admin Profile -->
    <div class="p-5 border-b border-slate-700/50">
        <div class="flex items-center gap-3">
            <div class="avatar placeholder">
                <div class="bg-warning text-warning-content rounded-full w-10 flex items-center justify-center">
                    <span class="text-sm font-bold">AD</span>
                </div>
            </div>
            <div class="flex-1 min-w-0">
                <h3 class="text-sm font-semibold text-white truncate">{{ Auth::user()->name }}</h3>
                <p class="text-xs text-slate-400 truncate">{{ Auth::user()->email }}</p>
            </div>
            <span class="badge badge-warning badge-sm text-xs">Super Admin</span>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 p-4 space-y-1 overflow-y-auto scrollbar-thin">

        @php
            $navItems = [
                [
                    'key' => 'dashboard',
                    'label' => 'Dashboard',
                    'route' => 'admin.dashboard',
                    'icon' =>
                        '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />',
                ],
                [
                    'key' => 'users',
                    'label' => 'Users',
                    'route' => '#',
                    'badge' => ['text' => '1.2K', 'class' => 'bg-info/20 text-info'],
                    'icon' =>
                        '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />',
                ],
                [
                    'key' => 'sellers',
                    'label' => 'Sellers',
                    'route' => 'admin.seller-management',
                    'badge' => ['text' => '8 New', 'class' => 'bg-warning/20 text-warning'],
                    'icon' =>
                        '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />',
                ],
                [
                    'key' => 'menu',
                    'label' => 'Menu & Categories',
                    'route' => '#',
                    'icon' =>
                        '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />',
                ],
                [
                    'key' => 'promotions',
                    'label' => 'Promotions',
                    'route' => '#',
                    'icon' =>
                        '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />',
                ],
            ];

            $navItems2 = [
                [
                    'key' => 'analytics',
                    'label' => 'Analytics & Reports',
                    'route' => '#',
                    'icon' =>
                        '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />',
                ],
                [
                    'key' => 'logs',
                    'label' => 'System Logs',
                    'route' => '#',
                    'icon' =>
                        '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4" />',
                ],
                [
                    'key' => 'settings',
                    'label' => 'Settings',
                    'route' => '#',
                    'icon' =>
                        '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />',
                ],
            ];
        @endphp

        @foreach ($navItems as $item)
            @php
                $url = $item['route'] !== '#' ? route($item['route']) : '#';

                $isActive = $item['route'] !== '#' && request()->routeIs($item['route']);

                $activeClass = 'bg-warning/10 text-warning border-l-2 border-warning';
                $inactiveClass = 'text-slate-400 hover:bg-slate-800 hover:text-slate-200 border-l-2 border-transparent';
            @endphp

            <a href="{{ $url }}"
                class="flex items-center gap-3 px-4 py-3 rounded-r-xl text-sm font-medium transition-all {{ $isActive ? $activeClass : $inactiveClass }}">

                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    {!! $item['icon'] !!}
                </svg>

                <span>{{ $item['label'] }}</span>

                @if (isset($item['badge']))
                    <span class="ml-auto text-xs font-semibold px-2 py-0.5 rounded-full {{ $item['badge']['class'] }}">
                        {{ $item['badge']['text'] }}
                    </span>
                @endif
            </a>
        @endforeach

        <!-- Divider -->
        <div class="border-t border-slate-700/50 my-3"></div>

        @foreach ($navItems2 as $item)
            @php
                $isActive = $active === $item['key'];
                $url = $item['route'] !== '#' ? route($item['route']) : '#';
            @endphp
            <a href="{{ $url }}"
                class="flex items-center gap-3 px-4 py-3 rounded-r-xl text-sm font-medium transition-all {{ $isActive ? $activeClass : $inactiveClass }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    {!! $item['icon'] !!}
                </svg>
                <span>{{ $item['label'] }}</span>
            </a>
        @endforeach

    </nav>

    <!-- Sidebar Footer -->
    <div class="p-4 border-t border-slate-700/50">
        <a href="{{ route('logout') }}"
            class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium text-red-400 hover:bg-red-500/10 transition-all"
            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
            </svg>
            Logout
        </a>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
            @csrf
        </form>
    </div>

</aside>
