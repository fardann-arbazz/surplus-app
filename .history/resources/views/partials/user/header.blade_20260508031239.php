<header class="sticky top-0 z-40 bg-white/80 backdrop-blur-lg shadow-sm border-b border-base-200">
    <!-- Desktop Header -->
    <div class="hidden lg:block">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Logo -->
                <div class="flex items-center gap-3">
                    <span class="text-xl font-bold">Rantangku</span>
                </div>

                <!-- Desktop Navigation -->
                <nav class="flex items-center gap-8">
                    <a href="{{ route('user.home') }}" class="text-sm font-semibold text-warning">Home</a>
                    <a href="{{ route('user.surplus-menu') }}"
                        class="text-sm font-medium text-base-content/70 hover:text-warning transition-colors">Orders</a>
                    <a href="#"
                        class="text-sm font-medium text-base-content/70 hover:text-warning transition-colors">Promos</a>
                    <a href="#"
                        class="text-sm font-medium text-base-content/70 hover:text-warning transition-colors">About</a>
                </nav>

                <!-- Right Section -->
                <div class="flex items-center gap-3">
                    <!-- Become a Seller Button - Desktop -->
                    <a href="{{ route('store.index') }}"
                        class="btn btn-ghost btn-sm text-base-content/70 hover:text-warning gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        Become a Seller
                    </a>

                    @include('partials.user.location-picker')

                    <!-- Cart Button Desktop -->
                    <button @click="toggleCart" class="btn btn-ghost btn-circle relative">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z" />
                        </svg>
                        <span x-show="cartCount > 0" x-text="cartCount" x-cloak
                            class="absolute -top-1 -right-1 w-5 h-5 bg-warning text-warning-content text-xs font-bold rounded-full flex items-center justify-center"></span>
                    </button>

                    <!-- Profile Dropdown - DaisyUI -->
                    <div class="dropdown dropdown-end">
                        <label tabindex="0" class="btn btn-ghost btn-sm gap-2">

                            <span class="text-sm font-medium">{{ Auth::user()->name }}</span>
                        </label>
                        <ul tabindex="0"
                            class="dropdown-content z-1 menu p-2 shadow-lg bg-base-100 rounded-box w-52 mt-2 border border-base-200">
                            <li><a href="#">My Profile</a></li>
                            <li><a href="#">My Orders</a></li>
                            <li><a href="#">Favorites</a></li>
                            <div class="divider my-1"></div>
                            <li><a href="#">Switch to Seller</a></li>
                            <form action="{{ route('logout') }}" method="post">
                                @csrf
                                <li><button type="submit" class="text-error">Logout</button></li>
                            </form>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile Header -->
    <div class="lg:hidden">
        <div class="px-4 py-3">
            <div class="flex items-center justify-between mb-3">
                <!-- Logo Mobile -->
                <div class="flex items-center gap-2">
                    <span class="text-lg font-bold">Rantangku</span>
                </div>

                <div class="flex items-center gap-2">
                    <!-- Become Seller Mobile -->
                    <a href="{{ route('store.index') }}" class="btn btn-ghost btn-xs text-base-content/70">
                        🏪 Sell
                    </a>

                    <!-- Cart Mobile -->
                    <button @click="toggleCart" class="btn btn-ghost btn-circle relative">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z" />
                        </svg>
                        <span x-show="cartCount > 0" x-text="cartCount" x-cloak
                            class="absolute -top-1 -right-1 w-5 h-5 bg-warning text-warning-content text-xs font-bold rounded-full flex items-center justify-center"></span>
                    </button>
                </div>
            </div>

            <!-- Search Bar Mobile -->
            <div class="relative">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-base-content/40" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <input type="text" placeholder="Search food or restaurant..."
                    class="input input-bordered w-full pl-10 bg-base-200/50 border-base-200 rounded-xl text-sm focus:outline-none focus:border-warning transition-colors">
            </div>
        </div>
    </div>
</header>
