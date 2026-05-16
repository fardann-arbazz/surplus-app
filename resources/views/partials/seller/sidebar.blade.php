    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
        class="fixed lg:static lg:translate-x-0 inset-y-0 left-0 w-64 bg-white border-r border-slate-200 z-50 transition-transform duration-300 flex flex-col">

        {{-- Logo --}}
        <div class="p-5 border-b border-slate-200">
            <div class="flex items-center gap-3">
                <div>
                    <span class="text-lg font-bold text-slate-800">Rantangku</span>
                    <p class="text-xs text-slate-500">Seller Dashboard</p>
                </div>
            </div>
        </div>

        {{-- Store Info --}}
        <div class="p-5 border-b border-slate-200 bg-orange-50/50">

            <div class="flex items-center gap-3">
                <img src="{{ $store->image_url }}" alt="Store" class="w-12 h-12 rounded-xl object-cover">
                <div>
                    <h3 class="text-sm font-semibold text-slate-800">{{ $store->name ?? 'Store Saya' }}</h3>
                    <div class="flex items-center gap-1 mt-0.5">
                        <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                        <span
                            class="text-xs text-green-600 font-medium">{{ $store->is_active === true ? 'Terverifikasi' : 'Belum Verifikasi' }}</span>
                    </div>
                </div>
            </div>
        </div>

        @php
            $navItems = [
                [
                    'key' => 'dashboard',
                    'label' => 'Dashboard',
                    'route' => 'seller.dashboard',
                    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />',
                ],
                [
                    'key' => 'orders',
                    'label' => 'Orders',
                    'route' => 'seller.orders.index',
                    'badge' => ['text' => $countOrder, 'class' => 'bg-orange-500 text-white'],
                    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2" />',
                ],
                [
                    'key' => 'menu',
                    'label' => 'Menu',
                    'route' => 'seller.menu-management',
                    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 6v6m0 0v6m0-6h6m-6 0H6" />',
                ],
                [
                    'key' => 'promotions',
                    'label' => 'Promotions',
                    'route' => '#',
                    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828" />',
                ],
                [
                    'key' => 'analytics',
                    'label' => 'Analytics',
                    'route' => '#',
                    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />',
                ],
                [
                    'key' => 'transactions',
                    'label' => 'Transactions',
                    'route' => '#',
                    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9" />',
                ],
                [
                    'key' => 'settings',
                    'label' => 'Settings',
                    'route' => '#',
                    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M10.325 4.317c.426-1.756..." />',
                ],
            ];

        @endphp

        {{-- Navigation --}}
        <nav class="flex-1 p-4 space-y-1 overflow-y-auto">

            @foreach ($navItems as $item)
                @php
                    $url = $item['route'] !== '#' ? route($item['route']) : '#';

                    $isActive = $item['route'] !== '#' && request()->routeIs($item['route']);

                    $activeClass = 'bg-orange-500 text-white';
                    $inactiveClass = ' text-slate-600 hover:bg-slate-100 ';
                @endphp

                <a href="{{ $url }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium {{ $isActive ? $activeClass : $inactiveClass }} transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        {!! $item['icon'] !!}
                    </svg>
                    {{ $item['label'] }}

                    @if (isset($item['badge']) && $item['badge']['text'] > 0)
                        <span class="ml-auto bg-orange-500 text-white text-xs font-bold px-2 py-0.5 rounded-full">
                            {{ $item['badge']['text'] }}
                        </span>
                    @endif
                </a>
            @endforeach
        </nav>

        {{-- Footer --}}
        <div class="p-4 border-t border-slate-200">
            <a href="#"
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-red-600 hover:bg-red-50 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
                Logout
            </a>
        </div>

    </aside>
