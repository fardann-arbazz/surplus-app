<header class="sticky top-0 z-30 bg-white border-b border-slate-200">
    <div class="flex items-center justify-between px-4 lg:px-6 py-3">

        {{-- Tombol menu mobile --}}
        <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden p-2 -ml-2 rounded-lg hover:bg-slate-100">
            <svg class="w-6 h-6 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>

        <div class="flex items-center gap-4 ml-auto">

            {{-- Notifikasi --}}
            <button class="relative p-2 rounded-xl hover:bg-slate-100 transition-colors">
                <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
                <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
            </button>

            {{-- Profile --}}
            <div class="dropdown dropdown-bottom dropdown-end">
                <div tabindex="0" role="button" class="flex items-center gap-2 cursor-pointer">
                    <div class="w-8 h-8 bg-orange-100 rounded-full flex items-center justify-center">
                        <span class="text-sm font-semibold text-orange-600">JD</span>
                    </div>
                    <span class="hidden sm:block text-sm font-medium text-slate-700">
                        {{ Auth::user()->name }}
                    </span>
                </div>

                <ul tabindex="0" class="dropdown-content menu bg-base-100 rounded-box z-1 w-52 p-2 mt-2 shadow">
                    <li><a>Profile</a></li>
                    <li><a>Settings</a></li>
                    <form action="{{ route('logout') }}", method="POST">
                        @csrf
                        <li><button type="submit" class="text-red-500">Logout</button></li>
                    </form>
                </ul>
            </div>

        </div>
    </div>
</header>
