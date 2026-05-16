@extends('layouts.user')

@section('title', 'Keranjang Belanja - Rantangku')

@section('content')
    <div class="min-h-screen bg-base-200 pb-32">

        {{-- ── TOPBAR ──────────────────────────────────────────────── --}}
        <div
            class="sticky top-0 z-20 bg-base-100/95 backdrop-blur border-b border-base-200 px-4 py-3 flex items-center gap-3">
            <a href="javascript:history.back()" class="btn btn-ghost btn-sm btn-circle">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <div class="flex-1">
                <h1 class="font-bold text-base">Keranjang Belanja</h1>
                @if ($cartItems->isNotEmpty())
                    <p class="text-xs text-base-content/40">{{ $cartItems->count() }} item</p>
                @endif
            </div>

            {{-- Kosongkan semua --}}
            @if ($cartItems->isNotEmpty())
                <form action="{{ route('user.cart.clear') }}" method="POST"
                    onsubmit="return confirm('Kosongkan seluruh keranjang?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-ghost btn-sm text-error text-xs">
                        Kosongkan
                    </button>
                </form>
            @endif
        </div>

        {{-- ── FLASH MESSAGES ──────────────────────────────────────── --}}
        @if (session('success'))
            <div class="mx-4 mt-3 p-3 bg-green-50 border border-green-200 text-green-700 rounded-xl text-sm text-center"
                id="flashMsg">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="mx-4 mt-3 p-3 bg-red-50 border border-red-200 text-red-600 rounded-xl text-sm text-center"
                id="flashMsg">
                {{ session('error') }}
            </div>
        @endif

        {{-- ── MULTI-STORE WARNING ──────────────────────────────────── --}}
        @if ($grouped->count() > 1)
            <div class="mx-4 mt-3 p-3 bg-amber-50 border border-amber-300 rounded-xl text-sm text-amber-700 flex gap-2">
                <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-2.694-.833-3.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z" />
                </svg>
                <span>
                    Kamu punya item dari <strong>{{ $grouped->count() }} toko berbeda</strong>.
                    Checkout hanya bisa untuk 1 toko — hapus item dari toko lain dulu ya.
                </span>
            </div>
        @endif

        {{-- ── EMPTY STATE ──────────────────────────────────────────── --}}
        @if ($cartItems->isEmpty())
            <div class="flex flex-col items-center justify-center min-h-[60vh] px-6 text-center">
                <div class="w-28 h-28 rounded-full bg-orange-50 flex items-center justify-center mb-5">
                    <svg class="w-14 h-14 text-orange-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M3 3h2l.4 2M7 13h10l4-10H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <h2 class="text-lg font-bold text-base-content mb-2">Keranjang Kosong</h2>
                <p class="text-sm text-base-content/50 mb-6 max-w-xs">
                    Yuk temukan surplus makanan enak di sekitar kamu!
                </p>
                <a href="{{ route('user.surplus-menu') }}"
                    class="btn bg-orange-500 hover:bg-orange-600 border-orange-500 text-white rounded-xl px-8 gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    Cari Surplus
                </a>
            </div>
        @else
            {{-- ── CART ITEMS (grouped per toko) ───────────────────── --}}
            <div class="px-4 pt-4 space-y-3">
                @foreach ($grouped as $storeId => $storeItems)
                    @php $store = $storeItems->first()->surplus->product->store; @endphp

                    <div class="bg-base-100 rounded-2xl overflow-hidden border border-base-200 shadow-sm">

                        {{-- Store header --}}
                        <div class="flex items-center gap-2.5 px-4 py-3 border-b border-base-200 bg-orange-50/50">
                            <div class="w-7 h-7 rounded-lg bg-orange-100 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            </div>
                            <span class="font-semibold text-sm text-base-content">
                                {{ $store->name ?? 'Toko' }}
                            </span>
                            <span class="text-xs text-base-content/40 ml-auto">
                                {{ $storeItems->count() }} item
                            </span>
                        </div>

                        {{-- Items --}}
                        @foreach ($storeItems as $item)
                            @php
                                $product = $item->surplus->product;
                                $img = $product->productImg->first()?->img_url ?? asset('images/placeholder-food.png');
                                $price = $item->surplus->discount_price ?? ($product->price ?? 0);
                                $maxStock = $item->surplus->remaining_quantity;
                                $isExpired = $item->surplus->expired_at?->isPast();
                            @endphp

                            <div
                                class="flex items-start gap-3 px-4 py-3.5 border-b border-base-200 last:border-0
                            {{ $isExpired ? 'opacity-60' : '' }}">

                                {{-- Product image --}}
                                <a href="{{ route('user.surplus.show', $item->surplus_id) }}"
                                    class="shrink-0 w-16 h-16 rounded-xl overflow-hidden bg-base-200">
                                    <img src="{{ $img }}" class="w-full h-full object-cover" alt="">
                                </a>

                                {{-- Info --}}
                                <div class="flex-1 min-w-0">
                                    <a href="{{ route('user.surplus.show', $item->surplus_id) }}">
                                        <p
                                            class="font-semibold text-sm text-base-content leading-snug line-clamp-2 hover:text-orange-500 transition-colors">
                                            {{ $product->name ?? '-' }}
                                        </p>
                                    </a>

                                    @if ($isExpired)
                                        <span class="text-xs text-red-500 font-medium">⚠️ Sudah kadaluarsa</span>
                                    @else
                                        <p class="text-orange-500 font-bold text-sm mt-0.5">
                                            Rp {{ number_format($price, 0, ',', '.') }}
                                        </p>
                                    @endif

                                    {{-- Quantity controls + delete --}}
                                    <div class="flex items-center gap-2 mt-2.5">

                                        {{-- Quantity form --}}
                                        @if (!$isExpired)
                                            <div class="flex items-center bg-base-200 rounded-xl overflow-hidden">
                                                <form action="{{ route('user.cart.update', $item->id) }}" method="POST"
                                                    class="qty-form" data-cart="{{ $item->id }}">
                                                    @csrf @method('PATCH')
                                                    <input type="hidden" name="quantity" class="qty-hidden-input"
                                                        value="{{ $item->quantity }}">

                                                    <button type="button"
                                                        onclick="adjustQty({{ $item->id }}, -1, {{ $maxStock }})"
                                                        class="w-8 h-8 flex items-center justify-center text-base-content/50
                                                               hover:text-orange-500 transition-colors font-bold text-lg">
                                                        −
                                                    </button>
                                                </form>

                                                <span id="qty-display-{{ $item->id }}"
                                                    class="text-sm font-bold text-base-content min-w-6 text-center">
                                                    {{ $item->quantity }}
                                                </span>

                                                <form action="{{ route('user.cart.update', $item->id) }}" method="POST"
                                                    class="qty-form" data-cart="{{ $item->id }}">
                                                    @csrf @method('PATCH')
                                                    <input type="hidden" name="quantity" class="qty-hidden-input"
                                                        value="{{ $item->quantity }}">
                                                    <button type="button"
                                                        onclick="adjustQty({{ $item->id }}, 1, {{ $maxStock }})"
                                                        class="w-8 h-8 flex items-center justify-center text-base-content/50
                                                               hover:text-orange-500 transition-colors font-bold text-lg">
                                                        +
                                                    </button>
                                                </form>
                                            </div>

                                            {{-- Subtotal --}}
                                            <span class="text-xs text-base-content/50 ml-1"
                                                id="subtotal-{{ $item->id }}">
                                                = Rp {{ number_format($item->subtotal(), 0, ',', '.') }}
                                            </span>
                                        @endif

                                        {{-- Delete --}}
                                        <form action="{{ route('user.cart.destroy', $item->id) }}" method="POST"
                                            class="ml-auto">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                class="btn btn-ghost btn-xs text-error hover:bg-red-50 rounded-lg px-2">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>

            {{-- ── ORDER SUMMARY ────────────────────────────────────── --}}
            <div class="mx-4 mt-3 bg-base-100 rounded-2xl border border-base-200 shadow-sm overflow-hidden">
                <div class="px-4 py-3 border-b border-base-200">
                    <p class="text-xs text-base-content/40 uppercase tracking-wider">Ringkasan</p>
                </div>
                <div class="px-4 py-3 space-y-2">
                    @foreach ($cartItems as $item)
                        @if (!$item->surplus->expired_at?->isPast())
                            <div class="flex justify-between text-sm">
                                <span class="text-base-content/60 truncate max-w-[60%]">
                                    {{ $item->surplus->product->name ?? '-' }}
                                    <span class="text-base-content/40">×{{ $item->quantity }}</span>
                                </span>
                                <span class="font-medium">
                                    Rp {{ number_format($item->subtotal(), 0, ',', '.') }}
                                </span>
                            </div>
                        @endif
                    @endforeach
                </div>
                <div class="flex items-center justify-between px-4 py-3 bg-orange-50 border-t border-orange-100">
                    <span class="font-semibold text-base-content">Total</span>
                    <span class="font-extrabold text-lg text-orange-500" id="grandTotal">
                        Rp {{ number_format($total, 0, ',', '.') }}
                    </span>
                </div>
            </div>

        @endif
    </div>

    {{-- ── BOTTOM CHECKOUT BAR ──────────────────────────────────────── --}}
    @if ($cartItems->isNotEmpty())
        <div class="fixed bottom-0 left-0 right-0 z-30 bg-base-100 border-t border-base-200 px-4 py-3 safe-area-bottom">
            <form action="{{ route('user.cart.checkout') }}" method="POST">
                @csrf
                <button type="submit" {{ $grouped->count() > 1 ? 'disabled' : '' }}
                    class="btn w-full rounded-xl text-white font-bold text-base gap-2 shadow-lg
                           {{ $grouped->count() > 1
                               ? 'bg-base-300 border-base-300 cursor-not-allowed'
                               : 'bg-orange-500 hover:bg-orange-600 border-orange-500' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    @if ($grouped->count() > 1)
                        Pilih 1 Toko dulu
                    @else
                        Checkout Sekarang
                    @endif
                </button>
            </form>
        </div>
    @endif

    @push('scripts')
        <script>
            // ── Flash auto-hide ─────────────────────────────────────────
            setTimeout(() => document.getElementById('flashMsg')?.remove(), 4000);

            // ── Quantity adjustment (optimistic UI + auto-submit) ───────
            const qtyState = {};

            @foreach ($cartItems as $item)
                qtyState[{{ $item->id }}] = {
                    qty: {{ $item->quantity }},
                    price: {{ $item->surplus->discount_price ?? ($item->surplus->product->price ?? 0) }},
                    max: {{ $item->surplus->remaining_quantity }},
                };
            @endforeach

            function adjustQty(cartId, delta, maxStock) {
                const state = qtyState[cartId];
                const newQty = Math.min(maxStock, Math.max(1, state.qty + delta));

                if (newQty === state.qty) return;

                state.qty = newQty;

                // Update display
                document.getElementById(`qty-display-${cartId}`).textContent = newQty;

                // Update subtotal display
                const subtotalEl = document.getElementById(`subtotal-${cartId}`);
                if (subtotalEl) {
                    const subtotal = state.price * newQty;
                    subtotalEl.textContent = '= Rp ' + subtotal.toLocaleString('id-ID');
                }

                // Update all hidden inputs for this cart item
                document.querySelectorAll(`.qty-form[data-cart="${cartId}"] .qty-hidden-input`).forEach(input => {
                    input.value = newQty;
                });

                // Debounce submit
                clearTimeout(state.timer);
                state.timer = setTimeout(() => submitQtyUpdate(cartId, newQty), 800);
            }

            function submitQtyUpdate(cartId, qty) {
                const form = document.querySelector(`.qty-form[data-cart="${cartId}"]`);
                if (!form) return;

                // Update hidden input sebelum submit
                form.querySelector('.qty-hidden-input').value = qty;
                form.submit();
            }
        </script>
        <style>
            .safe-area-bottom {
                padding-bottom: max(0.75rem, env(safe-area-inset-bottom));
            }

            .line-clamp-2 {
                display: -webkit-box;
                -webkit-line-clamp: 2;
                -webkit-box-orient: vertical;
                overflow: hidden;
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
