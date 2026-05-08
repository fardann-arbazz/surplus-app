{{-- resources/views/user/home.blade.php --}}
@extends('layouts.user')

@section('title', 'Rantangku - Food Delivery')

@section('content')
    <main>
        {{-- WAITING LOCATION STATE --}}
        <div id="homeWaitingLocation" class="flex flex-col items-center justify-center min-h-[60vh] px-4">
            <div class="w-24 h-24 rounded-full bg-warning/10 flex items-center justify-center mb-6 animate-pulse">
                <svg class="w-12 h-12 text-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
            </div>
            <h2 class="text-lg font-semibold mb-2">Atur Lokasi Dulu Yuk!</h2>
            <p class="text-sm text-base-content/60 text-center mb-6 max-w-xs">
                Biar kami bisa tampilin resto & menu terdekat buat kamu
            </p>
            <button onclick="LocationPicker.open()" class="btn btn-warning gap-2 px-8">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                Pilih Lokasi
            </button>
        </div>

        {{-- MAIN CONTENT --}}
        <div id="homeContent" class="hidden">

            {{-- Desktop Search & Filter Bar --}}
            <div class="hidden lg:flex items-center gap-4 mb-6">
                <div class="relative flex-1 max-w-2xl">
                    <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-base-content/40" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input type="text" placeholder="Search food, restaurant, or cuisine..." x-model="searchQuery"
                        class="input input-bordered w-full pl-12 bg-base-100 border-base-200 rounded-2xl text-sm focus:outline-none focus:border-warning transition-all shadow-sm">
                </div>
                <button class="btn btn-ghost border border-base-200 rounded-2xl text-sm font-medium gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                    </svg>
                    Filter
                </button>
            </div>

            {{-- PROMO BANNER SLIDER --}}
            <div class="carousel w-full rounded-3xl overflow-hidden shadow-sm mb-6 lg:mb-8">
                @include('partials.user.promo-banners')
            </div>

            {{-- CATEGORIES --}}
            <section class="mb-6 lg:mb-8">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg lg:text-xl font-bold">Categories</h2>
                    <a href="/categories" class="text-sm font-medium text-warning hover:underline">See All</a>
                </div>
                <div
                    class="flex gap-3 overflow-x-auto pb-2 scrollbar-hide -mx-4 px-4 lg:mx-0 lg:px-0 lg:grid lg:grid-cols-8 lg:gap-4">
                    @php
                        $categories = [
                            ['id' => 1, 'name' => 'Rice', 'icon' => '🍚', 'bg' => 'bg-orange-100'],
                            ['id' => 2, 'name' => 'Noodle', 'icon' => '🍜', 'bg' => 'bg-yellow-100'],
                            ['id' => 3, 'name' => 'Chicken', 'icon' => '🍗', 'bg' => 'bg-amber-100'],
                            ['id' => 4, 'name' => 'Seafood', 'icon' => '🦐', 'bg' => 'bg-blue-100'],
                            ['id' => 5, 'name' => 'Vegetables', 'icon' => '🥬', 'bg' => 'bg-green-100'],
                            ['id' => 6, 'name' => 'Drinks', 'icon' => '🥤', 'bg' => 'bg-cyan-100'],
                            ['id' => 7, 'name' => 'Snacks', 'icon' => '🍟', 'bg' => 'bg-red-100'],
                            ['id' => 8, 'name' => 'Dessert', 'icon' => '🍰', 'bg' => 'bg-pink-100'],
                        ];
                    @endphp
                    @foreach ($categories as $category)
                        <a href="/category/{{ $category['id'] }}"
                            class="shrink-0 flex flex-col items-center gap-2 group">
                            <div
                                class="w-16 h-16 lg:w-20 lg:h-20 rounded-2xl {{ $category['bg'] }} flex items-center justify-center text-2xl lg:text-3xl transition-all group-hover:shadow-lg">
                                <span>{{ $category['icon'] }}</span>
                            </div>
                            <span class="text-xs font-medium whitespace-nowrap">{{ $category['name'] }}</span>
                        </a>
                    @endforeach
                </div>
            </section>

            {{-- NEARBY STORES SECTION --}}
            <section class="mb-6 lg:mb-8">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 class="text-lg lg:text-xl font-bold">Nearby Restaurants</h2>
                        <p class="text-sm text-base-content/60">Popular stores around you</p>
                    </div>
                    <a href="/stores" class="text-sm font-medium text-warning hover:underline">See All</a>
                </div>

                {{-- Stores Skeleton --}}
                <div id="storesSkeleton" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                    @for ($i = 0; $i < 4; $i++)
                        <div class="card bg-base-100 shadow-sm overflow-hidden">
                            <div class="skeleton h-40 lg:h-48 w-full rounded-none"></div>
                            <div class="p-4 space-y-2">
                                <div class="skeleton h-4 w-3/4 rounded"></div>
                                <div class="skeleton h-3 w-1/2 rounded"></div>
                                <div class="flex justify-between mt-2">
                                    <div class="skeleton h-3 w-16 rounded"></div>
                                    <div class="skeleton h-3 w-12 rounded"></div>
                                </div>
                            </div>
                        </div>
                    @endfor
                </div>

                {{-- Stores Grid --}}
                <div id="nearbyStoresGrid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                </div>

                {{-- Stores Empty --}}
                <div id="storesEmpty" class="hidden text-center py-10">
                    <p class="text-base-content/50">Tidak ada toko terdekat</p>
                    <button onclick="HomeApp.retryStores()" class="btn btn-outline btn-sm mt-3">Refresh</button>
                </div>

                {{-- Stores Error --}}
                <div id="storesError" class="hidden text-center py-10">
                    <div class="text-error mb-2">⚠️</div>
                    <p class="text-sm text-base-content/60 error-message">Gagal memuat toko</p>
                    <button onclick="HomeApp.retryStores()" class="btn btn-warning btn-sm mt-3 gap-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        Coba Lagi
                    </button>
                </div>

                {{-- Stores Load More --}}
                <div id="storesLoadMore" class="text-center py-4 hidden">
                    <button onclick="HomeApp.loadMoreStores()" class="btn btn-outline btn-sm">
                        Muat Lebih Banyak
                    </button>
                </div>
            </section>

            {{-- RECOMMENDED MENUS SECTION --}}
            <section>
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 class="text-lg lg:text-xl font-bold">Recommended for You</h2>
                        <p class="text-sm text-base-content/60">Surplus food at best prices</p>
                    </div>
                    <a href="/surplus-menu" class="text-sm font-medium text-warning hover:underline">See All</a>
                </div>

                {{-- Menus Skeleton --}}
                <div id="menusSkeleton" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
                    @for ($i = 0; $i < 5; $i++)
                        <div class="card bg-base-100 shadow-sm overflow-hidden">
                            <div class="skeleton h-36 lg:h-44 w-full rounded-none"></div>
                            <div class="p-3 space-y-2">
                                <div class="skeleton h-4 w-full rounded"></div>
                                <div class="skeleton h-3 w-3/4 rounded"></div>
                                <div class="flex justify-between mt-2">
                                    <div class="skeleton h-4 w-20 rounded"></div>
                                    <div class="skeleton h-3 w-10 rounded"></div>
                                </div>
                            </div>
                        </div>
                    @endfor
                </div>

                {{-- Menus Grid --}}
                <div id="recommendedMenusGrid"
                    class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
                </div>

                {{-- Menus Empty --}}
                <div id="menusEmpty" class="hidden text-center py-10">
                    <p class="text-base-content/50">Belum ada menu rekomendasi</p>
                    <button onclick="HomeApp.retryMenus()" class="btn btn-outline btn-sm mt-3">Refresh</button>
                </div>

                {{-- Menus Error --}}
                <div id="menusError" class="hidden text-center py-10">
                    <div class="text-error mb-2">⚠️</div>
                    <p class="text-sm text-base-content/60 error-message">Gagal memuat menu</p>
                    <button onclick="HomeApp.retryMenus()" class="btn btn-warning btn-sm mt-3 gap-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        Coba Lagi
                    </button>
                </div>

                {{-- Menus Load More --}}
                <div id="menusLoadMore" class="text-center py-4 hidden">
                    <button onclick="HomeApp.loadMoreMenus()" class="btn btn-outline btn-sm">
                        Muat Lebih Banyak
                    </button>
                </div>
            </section>

        </div>
    </main>
@endsection

@push('scripts')
    @include('partials.user.home-scripts')
@endpush
