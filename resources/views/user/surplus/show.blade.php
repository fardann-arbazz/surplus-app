@extends('layouts.user')

@section('title', ($surplus->product->name ?? 'Detail Produk') . ' - Rantangku')

@section('content')
    <div class="min-h-screen bg-base-200 pb-24">

        {{-- ── TOP NAV ─────────────────────────────────────────────── --}}
        <div
            class="sticky top-0 z-20 bg-base-100/95 backdrop-blur border-b border-base-200 px-4 py-3 flex items-center gap-3">
            <a href="javascript:history.back()" class="btn btn-ghost btn-sm btn-circle">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <h1 class="font-bold text-base flex-1 truncate">Detail Produk</h1>

            {{-- Cart badge --}}
            @auth
                <a href="{{ route('user.cart.index') }}" class="btn btn-ghost btn-sm btn-circle relative">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 3h2l.4 2M7 13h10l4-10H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    @php
                        $cartCount = app(\App\Services\Cart\CartService::class)->getCount(auth()->user());
                    @endphp
                    @if ($cartCount > 0)
                        <span
                            class="absolute -top-0.5 -right-0.5 bg-orange-500 text-white text-[10px] font-bold w-4 h-4 rounded-full flex items-center justify-center">
                            {{ $cartCount > 9 ? '9+' : $cartCount }}
                        </span>
                    @endif
                </a>
            @endauth
        </div>

        {{-- ── GALLERY ─────────────────────────────────────────────── --}}
        @php
            $images = $surplus->product->productImg ?? collect();
            $mainImg = $images->first()?->img_url ?? asset('images/placeholder-food.png');
        @endphp

        <div class="relative bg-base-100">
            {{-- Main image --}}
            <div class="relative h-72 overflow-hidden" id="mainImgWrap">
                <img id="mainImg" src="{{ $mainImg }}" alt="{{ $surplus->product->name ?? '' }}"
                    class="w-full h-full object-cover transition-all duration-500">

                {{-- Overlay gradient --}}
                <div class="absolute inset-0 bg-linear-to-t from-base-100/60 to-transparent pointer-events-none"></div>

                {{-- SURPLUS badge --}}
                <div
                    class="absolute top-3 left-3 bg-orange-500 text-white text-xs font-bold px-3 py-1 rounded-full shadow-lg">
                    🔥 SURPLUS
                </div>

                {{-- Expiry countdown --}}
                @if ($surplus->expired_at)
                    @php
                        $hoursLeft = max(0, now()->diffInMinutes($surplus->expired_at, false) / 60);
                    @endphp
                    <div
                        class="absolute top-3 right-3 text-white text-xs font-semibold px-3 py-1.5 rounded-full shadow-lg flex items-center gap-1.5
                    {{ $hoursLeft < 1 ? 'bg-red-500' : ($hoursLeft < 3 ? 'bg-amber-500' : 'bg-green-600') }}">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10" />
                            <path d="M12 6v6l4 2" />
                        </svg>
                        <span id="countdown" data-expires="{{ $surplus->expired_at->toISOString() }}">
                            Sampai {{ $surplus->expired_at->format('H:i') }}
                        </span>
                    </div>
                @endif
            </div>

            {{-- Thumbnail strip --}}
            @if ($images->count() > 1)
                <div class="flex gap-2 px-4 py-3 overflow-x-auto scrollbar-hide bg-base-100">
                    @foreach ($images as $img)
                        <button onclick="switchImage('{{ $img->img_url }}')"
                            class="w-14 h-14 rounded-xl overflow-hidden flex-shrink-0 border-2 border-transparent hover:border-orange-400 transition-all thumbnail-btn">
                            <img src="{{ $img->img_url }}" class="w-full h-full object-cover" alt="">
                        </button>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- ── PRODUCT INFO ─────────────────────────────────────────── --}}
        <div class="bg-base-100 px-4 py-5 mb-2">

            {{-- Category --}}
            <span class="text-[11px] text-orange-500 font-semibold uppercase tracking-wider">
                {{ $surplus->product->category->name ?? 'Surplus' }}
            </span>

            {{-- Name --}}
            <h2 class="text-xl font-bold text-base-content mt-1 leading-tight">
                {{ $surplus->product->name ?? 'Produk Surplus' }}
            </h2>

            {{-- Price --}}
            <div class="flex items-baseline gap-3 mt-3">
                <span class="text-2xl font-extrabold text-orange-500">
                    Rp {{ number_format($surplus->discount_price ?? 0, 0, ',', '.') }}
                </span>
                @if (isset($surplus->product->price) && $surplus->product->price > ($surplus->discount_price ?? 0))
                    @php
                        $disc = round(
                            (($surplus->product->price - $surplus->discount_price) / $surplus->product->price) * 100,
                        );
                    @endphp
                    <span class="text-sm text-base-content/40 line-through">
                        Rp {{ number_format($surplus->product->price, 0, ',', '.') }}
                    </span>
                    <span
                        class="badge badge-sm text-white border-0
                    {{ $disc >= 50 ? 'bg-red-500' : ($disc >= 30 ? 'bg-amber-500' : 'bg-green-500') }}">
                        {{ $disc }}% OFF
                    </span>
                @endif
            </div>

            {{-- Stock bar --}}
            <div class="mt-4">
                @php
                    $stockPct =
                        $surplus->quantity > 0
                            ? min(100, round(($surplus->remaining_quantity / $surplus->quantity) * 100))
                            : 0;
                    $stockColor = $stockPct > 50 ? 'bg-green-500' : ($stockPct > 20 ? 'bg-amber-500' : 'bg-red-500');
                @endphp
                <div class="flex items-center justify-between text-xs text-base-content/50 mb-1.5">
                    <span>Sisa stok</span>
                    <span class="font-semibold {{ $stockPct <= 20 ? 'text-red-500' : 'text-base-content/70' }}">
                        {{ $surplus->remaining_quantity }} tersisa
                    </span>
                </div>
                <div class="w-full h-2 bg-base-200 rounded-full overflow-hidden">
                    <div class="h-full rounded-full transition-all {{ $stockColor }}"
                        style="width: {{ $stockPct }}%"></div>
                </div>
                @if ($surplus->remaining_quantity <= 5)
                    <p class="text-xs text-red-500 font-medium mt-1">⚡ Hampir habis! Segera pesan.</p>
                @endif
            </div>
        </div>

        {{-- ── STORE INFO ───────────────────────────────────────────── --}}
        <div class="bg-base-100 px-4 py-4 mb-2">
            <p class="text-xs text-base-content/40 uppercase tracking-wider mb-3">Toko</p>
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-orange-100 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-semibold text-base-content text-sm">
                        {{ $surplus->product->store->name ?? '-' }}
                    </p>
                    @if ($surplus->product->store?->address)
                        <p class="text-xs text-base-content/50 truncate mt-0.5">
                            {{ $surplus->product->store->address }}
                        </p>
                    @endif
                </div>
                @if (isset($surplus->distance_km))
                    <span class="text-xs text-base-content/40 shrink-0">
                        {{ number_format($surplus->distance_km, 1) }} km
                    </span>
                @endif
            </div>
        </div>

        {{-- ── DESCRIPTION ─────────────────────────────────────────── --}}
        @if ($surplus->product->description ?? null)
            <div class="bg-base-100 px-4 py-4 mb-2">
                <p class="text-xs text-base-content/40 uppercase tracking-wider mb-2">Deskripsi</p>
                <p class="text-sm text-base-content/80 leading-relaxed">
                    {{ $surplus->product->description }}
                </p>
            </div>
        @endif

        {{-- ── PRODUK LAIN DARI TOKO INI ───────────────────────────── --}}
        @if ($relatedSurplus->isNotEmpty())
            <div class="bg-base-100 px-4 py-4 mb-2">
                <p class="text-xs text-base-content/40 uppercase tracking-wider mb-3">Lainnya dari toko ini</p>
                <div class="flex gap-3 overflow-x-auto pb-1 scrollbar-hide">
                    @foreach ($relatedSurplus as $rel)
                        <a href="{{ route('user.surplus.show', $rel->id) }}"
                            class="shrink-0 w-32 card bg-base-200 overflow-hidden hover:shadow-md transition-all">
                            <figure class="h-24 overflow-hidden">
                                <img src="{{ $rel->product->productImg->first()?->img_url ?? asset('images/placeholder-food.png') }}"
                                    class="w-full h-full object-cover" alt="">
                            </figure>
                            <div class="p-2">
                                <p class="text-xs font-medium truncate">{{ $rel->product->name ?? '-' }}</p>
                                <p class="text-xs text-orange-500 font-bold mt-0.5">
                                    Rp {{ number_format($rel->discount_price ?? 0, 0, ',', '.') }}
                                </p>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

    </div>

    {{-- ── BOTTOM ADD TO CART BAR ──────────────────────────────────── --}}
    @php $isExpired = $surplus->expired_at && $surplus->expired_at->isPast(); @endphp
    @php $isOutOfStock = $surplus->remaining_quantity <= 0; @endphp

    <div class="fixed bottom-0 left-0 right-0 z-30 bg-base-100 border-t border-base-200 px-4 py-3 safe-area-bottom">

        {{-- Flash messages --}}
        @if (session('success'))
            <div class="mb-2 p-2.5 bg-green-50 border border-green-200 text-green-700 rounded-xl text-sm text-center font-medium"
                id="successFlash">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error') || $errors->any())
            <div class="mb-2 p-2.5 bg-red-50 border border-red-200 text-red-600 rounded-xl text-sm text-center"
                id="errorFlash">
                {{ session('error') ?? $errors->first() }}
            </div>
        @endif

        @if ($isExpired)
            <div class="btn btn-disabled w-full rounded-xl">
                Surplus Sudah Kadaluarsa
            </div>
        @elseif ($isOutOfStock)
            <div class="btn btn-disabled w-full rounded-xl">
                Stok Habis
            </div>
        @elseif (!auth()->check())
            {{-- Belum login --}}
            <a href="{{ route('login') }}?redirect={{ urlencode(request()->fullUrl()) }}"
                class="btn w-full rounded-xl bg-orange-500 hover:bg-orange-600 border-orange-500 text-white font-bold">
                Login untuk Pesan
            </a>
        @else
            {{-- Form add to cart --}}
            <form action="{{ route('user.cart.store') }}" method="POST" id="addToCartForm">
                @csrf
                <input type="hidden" name="surplus_id" value="{{ $surplus->id }}">

                <div class="flex items-center gap-3">
                    {{-- Quantity selector --}}
                    <div class="flex items-center gap-2 bg-base-200 rounded-xl px-3 py-2">
                        <button type="button" onclick="changeQty(-1)"
                            class="w-7 h-7 rounded-lg flex items-center justify-center text-base-content/60
                                   hover:bg-base-300 hover:text-orange-500 transition-all font-bold text-lg">
                            −
                        </button>
                        <input type="number" id="qtyInput" name="quantity" value="{{ $cartItem?->quantity ?? 1 }}"
                            min="1" max="{{ $surplus->remaining_quantity }}" readonly
                            class="w-8 text-center text-sm font-bold bg-transparent border-none outline-none">
                        <button type="button" onclick="changeQty(1)"
                            class="w-7 h-7 rounded-lg flex items-center justify-center text-base-content/60
                                   hover:bg-base-300 hover:text-orange-500 transition-all font-bold text-lg">
                            +
                        </button>
                    </div>

                    {{-- Add to cart button --}}
                    <button type="submit"
                        class="flex-1 btn rounded-xl bg-orange-500 hover:bg-orange-600 border-orange-500
                               text-white font-bold gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 3h2l.4 2M7 13h10l4-10H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        @if ($cartItem)
                            Update Keranjang
                        @else
                            Tambah ke Keranjang
                        @endif
                    </button>
                </div>
            </form>
        @endif
    </div>

    @push('scripts')
        <script>
            // ── Quantity control ────────────────────────────────────────
            const maxQty = {{ $surplus->remaining_quantity }};

            function changeQty(delta) {
                const input = document.getElementById('qtyInput');
                const newVal = Math.min(maxQty, Math.max(1, parseInt(input.value) + delta));
                input.value = newVal;
            }

            // ── Image gallery ───────────────────────────────────────────
            function switchImage(url) {
                const img = document.getElementById('mainImg');
                img.style.opacity = '0';
                img.style.transform = 'scale(1.03)';
                setTimeout(() => {
                    img.src = url;
                    img.style.opacity = '1';
                    img.style.transform = 'scale(1)';
                }, 200);
            }

            // ── Flash auto-hide ─────────────────────────────────────────
            setTimeout(() => {
                document.getElementById('successFlash')?.remove();
                document.getElementById('errorFlash')?.remove();
            }, 4000);

            // ── Expiry countdown ────────────────────────────────────────
            const countdownEl = document.getElementById('countdown');
            if (countdownEl) {
                const expiresAt = new Date(countdownEl.dataset.expires);

                function updateCountdown() {
                    const diff = Math.max(0, expiresAt - new Date());
                    const h = Math.floor(diff / 3600000);
                    const m = Math.floor((diff % 3600000) / 60000);
                    const s = Math.floor((diff % 60000) / 1000);

                    if (diff <= 0) {
                        countdownEl.textContent = 'Sudah kadaluarsa';
                        return;
                    }

                    if (h > 0) {
                        countdownEl.textContent = `${h}j ${m}m lagi`;
                    } else if (m > 0) {
                        countdownEl.textContent = `${m}m ${s}d lagi`;
                    } else {
                        countdownEl.textContent = `${s}d lagi`;
                    }
                }

                updateCountdown();
                setInterval(updateCountdown, 1000);
            }
        </script>
        <style>
            #mainImg {
                transition: opacity 0.2s ease, transform 0.2s ease;
            }

            .safe-area-bottom {
                padding-bottom: max(0.75rem, env(safe-area-inset-bottom));
            }

            .scrollbar-hide {
                -ms-overflow-style: none;
                scrollbar-width: none;
            }

            .scrollbar-hide::-webkit-scrollbar {
                display: none;
            }
        </style>
    @endpush

@endsection
