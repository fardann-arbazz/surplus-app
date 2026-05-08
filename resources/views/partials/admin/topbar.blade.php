{{--
    Partial: components/admin/topbar.blade.php
    Gunakan $pageTitle untuk judul halaman, contoh:
    @include('components.admin.topbar', ['pageTitle' => 'Dashboard Overview'])
--}}

<header class="sticky top-0 z-30 bg-base-100 border-b border-base-200 shadow-sm">
    <div class="flex items-center justify-between px-4 lg:px-6 py-3">

        <div class="flex items-center gap-4">
            <!-- Mobile menu toggle -->
            <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden btn btn-ghost btn-sm btn-circle">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>

            <!-- Breadcrumb -->
            <div class="hidden sm:block">
                <div class="flex items-center gap-2 text-sm">
                    <span class="text-base-content/50">Admin</span>
                    <span class="text-base-content/30">/</span>
                    <span class="font-medium">{{ $pageTitle ?? 'Dashboard' }}</span>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-3">

            <!-- Global Search -->
            <div class="hidden md:block relative">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-base-content/40" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <input type="text" placeholder="Search anything..."
                    class="input input-bordered input-sm w-64 pl-10 bg-base-200/50 text-sm focus:outline-none focus:border-warning">
                <kbd
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-base-content/30 bg-base-300 px-1.5 py-0.5 rounded">⌘K</kbd>
            </div>

            <!-- Notifications -->
            <div class="dropdown dropdown-end" onclick="markAllAsRead(this)">
                <label tabindex="0" class="btn btn-ghost btn-sm btn-circle relative">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    @if ($unreadCount > 0)
                        <span
                            class="notif-badge absolute -top-1 -right-1 text-[10px] font-bold min-w-4.5 h-4.5 flex items-center justify-center bg-error text-white rounded-full px-1">
                            {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                        </span>
                    @endif
                </label>
                <div tabindex="0"
                    class="dropdown-content z-1 menu p-2 shadow-lg bg-base-100 rounded-box w-80 mt-2 border border-base-200">
                    <div class="px-3 py-2 border-b border-base-200">
                        <p class="text-sm font-semibold">Notifications</p>
                    </div>
                    <div class="max-h-64 overflow-y-auto">
                        @isset($notifications)
                            @foreach ($notifications as $notif)
                                <div data-unread="{{ $notif->read_at === null ? 'true' : 'false' }}"
                                    class="px-3 py-3 hover:bg-base-200 rounded-lg cursor-pointer transition-colors {{ $notif->read_at === null ? 'bg-base-100' : '' }}">
                                    <div class="flex gap-3">
                                        {{-- Icon Store --}}
                                        <div
                                            class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center shrink-0">
                                            <span class="text-xl">
                                                {{ $notif->data['icon'] ?? '🏪' }}
                                            </span>
                                        </div>

                                        <div class="flex-1 min-w-0">
                                            {{-- Nama Toko + Waktu --}}
                                            <div class="flex items-center justify-between gap-2">
                                                <p class="text-sm font-bold truncate">
                                                    {{ $notif->data['name'] ?? 'Toko Saya' }}
                                                </p>
                                                <p class="text-[10px] text-base-content/40 shrink-0">
                                                    {{ $notif->created_at->diffForHumans() }}
                                                </p>
                                            </div>

                                            {{-- Pesan --}}
                                            <p class="text-xs text-base-content/70 mt-0.5">
                                                {{ $notif->data['message'] ?? '-' }}
                                            </p>

                                            {{-- Optional: Tombol aksi (jika ada) --}}
                                            @if (isset($notif->data['action_url']))
                                                <button class="btn btn-xs btn-ghost text-primary mt-2 p-0 h-auto">
                                                    Lihat Detail →
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endisset
                    </div>
                    <div class="px-3 py-2 border-t border-base-200 text-center">
                        <a href="#" class="text-xs text-warning font-medium">View all</a>
                    </div>
                </div>
            </div>

            <!-- Profile Dropdown -->
            <div class="dropdown dropdown-end">
                <label tabindex="0" class="btn btn-ghost btn-sm gap-2">
                    <div class="avatar placeholder">
                        <div class="bg-warning text-warning-content rounded-full w-8 flex items-center justify-center">
                            <span class="text-xs font-bold">AD</span>
                        </div>
                    </div>
                    <svg class="w-4 h-4 text-base-content/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </label>
                <ul tabindex="0"
                    class="dropdown-content z-1 menu p-2 shadow-lg bg-base-100 rounded-box w-52 mt-2 border border-base-200">
                    <li><a href="#">My Profile</a></li>
                    <li><a href="#">Account Settings</a></li>
                    <div class="divider my-1"></div>
                    <li>
                        <a href="{{ route('logout') }}" class="text-error"
                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            Logout
                        </a>
                    </li>
                </ul>
            </div>

        </div>
    </div>
</header>

@push('scripts')
    <script>
        function markAllAsRead(el) {

            const badge = document.querySelector('.notif-badge');

            // kalau tidak ada unread => stop
            if (!badge) return;

            fetch('/notifications/read-all', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(() => {
                    // hapus badge langsung tanpa reload
                    const badge = document.querySelector('.notif-badge');
                    if (badge) badge.remove();

                    // ubah style notif jadi read
                    document.querySelectorAll('[data-unread="true"]').forEach(el => {
                        el.classList.remove('bg-base-100');
                    });
                })
                .catch(err => console.error(err));
        }
    </script>
@endpush
