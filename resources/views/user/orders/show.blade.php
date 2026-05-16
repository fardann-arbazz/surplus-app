@extends('layouts.user')

@section('title', 'Detail Pesanan #' . $order->id)

@push('head')
    {{-- Midtrans Snap.js --}}
    @if (config('midtrans.is_production'))
        <script src="https://app.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
    @else
        <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}">
        </script>
    @endif
@endpush

@section('content')
    <div class="min-h-screen pb-10">

        {{-- ── TOP NAV ─────────────────────────────────────────────── --}}
        <div
            class="sticky top-0 z-20 bg-base-100/95 backdrop-blur border-b border-base-200 px-4 py-3 flex items-center gap-3">
            <a href="{{ route('user.orders.index') }}" class="btn btn-ghost btn-sm btn-circle">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <div class="flex-1">
                <h1 class="font-bold text-base">Detail Pesanan</h1>
                <p class="text-xs text-base-content/40">#{{ $order->id }}</p>
            </div>
        </div>

        <div class="max-w-xl sm:max-w-187.5 mx-auto px-4 pt-5 space-y-3">

            {{-- ── FLASH MESSAGES ────────────────────────────────────── --}}
            @if (session('success'))
                <div class="p-3 bg-green-50 border border-green-200 text-green-700 rounded-xl text-sm text-center"
                    id="flashMsg">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="p-3 bg-red-50 border border-red-200 text-red-600 rounded-xl text-sm text-center" id="flashMsg">
                    {{ session('error') }}
                </div>
            @endif

            {{-- ══════════════════════════════════════════════════════
             STATUS + PAYMENT BLOCK
             Layout berbeda tergantung status order
             ══════════════════════════════════════════════════════ --}}

            @if ($order->isPending() && !$order->isPaymentExpired())
                {{-- ── PENDING: Belum bayar, snap_token tersedia ────────── --}}
                <div class="bg-base-100 rounded-2xl overflow-hidden border border-orange-200 shadow-sm">

                    {{-- Header status --}}
                    <div class="bg-orange-50 px-4 py-3 border-b border-orange-100 flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-orange-100 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-orange-500 animate-pulse" fill="currentColor" viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10" />
                            </svg>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-bold text-orange-700">Menunggu Pembayaran</p>
                            <p class="text-xs text-orange-500">
                                Selesaikan sebelum
                                <strong>{{ $order->expires_at->format('H:i') }}</strong>
                            </p>
                        </div>
                        {{-- Live countdown --}}
                        <div id="countdownBadge" data-expires="{{ $order->expires_at->toISOString() }}"
                            class="text-xs font-bold text-white bg-orange-500 px-2.5 py-1 rounded-full tabular-nums">
                            --:--
                        </div>
                    </div>

                    {{-- Payment body --}}
                    <div class="px-4 py-5 text-center">
                        <p class="text-xs text-base-content/50 mb-1">Total yang harus dibayar</p>
                        <p class="text-3xl font-extrabold text-orange-500 mb-1">
                            Rp {{ number_format($order->total_price, 0, ',', '.') }}
                        </p>
                        <p class="text-xs text-base-content/40 mb-5">
                            Order #{{ $order->id }} · {{ $order->created_at->format('d M Y, H:i') }}
                        </p>

                        @if ($snapToken)
                            <button id="payBtn" onclick="triggerSnap()"
                                class="btn w-full bg-orange-500 hover:bg-orange-600 border-orange-500
                                       text-white font-bold rounded-xl gap-2 shadow-lg shadow-orange-500/30 text-base">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                </svg>
                                Bayar Sekarang
                            </button>
                            <p class="text-[11px] text-base-content/30 mt-2">
                                🔒 Pembayaran aman via Midtrans
                            </p>
                        @else
                            {{-- snap_token gagal di-generate (Midtrans error) --}}
                            <div class="p-3 bg-red-50 border border-red-200 rounded-xl text-sm text-red-600 mb-3">
                                Gagal memuat metode pembayaran. Coba refresh halaman ini.
                            </div>
                            <button onclick="window.location.reload()"
                                class="btn btn-outline border-orange-400 text-orange-500 hover:bg-orange-50 rounded-xl gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                                Refresh Halaman
                            </button>
                        @endif
                    </div>
                </div>
            @elseif ($order->isPending() && $order->isPaymentExpired())
                {{-- ── EXPIRED: Waktu bayar habis ──────────────────────── --}}
                <div class="bg-base-100 rounded-2xl border border-base-200 shadow-sm overflow-hidden">
                    <div class="bg-base-200 px-4 py-3 border-b border-base-200 flex items-center gap-3">
                        <div
                            class="w-8 h-8 rounded-full bg-base-300 flex items-center justify-center shrink-0 text-base-content/40">
                            ⏰
                        </div>
                        <div>
                            <p class="text-sm font-bold text-base-content/60">Waktu Pembayaran Habis</p>
                            <p class="text-xs text-base-content/40">Order otomatis dibatalkan</p>
                        </div>
                    </div>
                    <div class="px-4 py-4 text-center">
                        <p class="text-sm text-base-content/60 mb-4">
                            Ingin produk ini? Kamu bisa pesan lagi jika stok masih tersedia.
                        </p>
                        <a href="{{ route('user.surplus-menu') }}"
                            class="btn bg-orange-500 hover:bg-orange-600 border-orange-500 text-white rounded-xl gap-2">
                            Cari Surplus Lagi
                        </a>
                    </div>
                </div>
            @elseif ($order->status->value === 'paid')
                {{-- ── PAID: Sudah bayar, menunggu seller siapkan ─────── --}}
                <div class="bg-base-100 rounded-2xl border border-blue-200 shadow-sm overflow-hidden">
                    <div class="bg-blue-50 px-4 py-3 border-b border-blue-100 flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center shrink-0 text-lg">
                            ✅
                        </div>
                        <div>
                            <p class="text-sm font-bold text-blue-700">Pembayaran Berhasil</p>
                            <p class="text-xs text-blue-500">Seller sedang menyiapkan pesananmu</p>
                        </div>
                    </div>
                    <div class="px-4 py-3 text-center text-sm text-base-content/60">
                        Kamu akan mendapat notifikasi saat pesanan siap diambil.
                    </div>
                </div>
            @elseif ($order->status->value === 'ready_for_pickup')
                {{-- ── READY: Siap diambil, perlu kode pickup ──────────── --}}
                <div class="bg-base-100 rounded-2xl border border-orange-300 shadow-sm overflow-hidden">
                    <div class="bg-orange-50 px-4 py-3 border-b border-orange-100 flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-orange-100 flex items-center justify-center shrink-0 text-lg">
                            📦
                        </div>
                        <div>
                            <p class="text-sm font-bold text-orange-700">Pesanan Siap Diambil!</p>
                            <p class="text-xs text-orange-500">Datang ke toko dan masukkan kode dari seller</p>
                        </div>
                    </div>
                    <div class="px-4 py-4 text-center">
                        <a href="{{ route('user.orders.confirm-pickup', $order->id) }}"
                            class="btn w-full bg-orange-600 hover:bg-orange-700 border-orange-600
                              text-white font-bold rounded-xl gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Input Kode Pickup
                        </a>
                    </div>
                </div>
            @elseif ($order->status->value === 'completed')
                {{-- ── COMPLETED ────────────────────────────────────────── --}}
                <div class="bg-base-100 rounded-2xl border border-green-200 shadow-sm overflow-hidden">
                    <div class="bg-green-50 px-4 py-3 flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center shrink-0 text-lg">
                            🎉
                        </div>
                        <div>
                            <p class="text-sm font-bold text-green-700">Pesanan Selesai</p>
                            <p class="text-xs text-green-500">
                                Terima kasih telah menyelamatkan makanan surplus!
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            {{-- ── INFO TOKO ────────────────────────────────────────────── --}}
            <div class="bg-base-100 rounded-2xl border border-base-200 shadow-sm p-4">
                <p class="text-xs text-base-content/40 uppercase tracking-wider mb-3">Toko</p>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-orange-100 flex items-center justify-center shrink-0">
                        <img src="{{ $order->stores->image_url }}" alt="foto stores" class="rounded-md">
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-base-content">{{ $order->stores->name ?? '-' }}</p>
                        @if ($order->stores?->address)
                            <p class="text-xs text-base-content/50 mt-0.5 truncate">
                                📍 {{ $order->stores->address }}
                            </p>
                        @endif
                    </div>
                </div>
            </div>

            {{-- ── DAFTAR PRODUK ────────────────────────────────────────── --}}
            <div class="bg-base-100 rounded-2xl border border-base-200 shadow-sm overflow-hidden">
                <p class="px-4 py-3 font-semibold text-sm text-base-content border-b border-base-200">
                    Produk Dipesan
                </p>

                @foreach ($order->orderItems as $item)
                    @php $img = $item->surplusProduct->product->productImg->first(); @endphp
                    <div class="flex items-center gap-3 px-4 py-3.5 border-b border-base-200 last:border-0">
                        @if ($img)
                            <img src="{{ $img->img_url }}" class="w-14 h-14 rounded-xl object-cover shrink-0"
                                alt="">
                        @else
                            <div
                                class="w-14 h-14 rounded-xl bg-base-200 shrink-0 flex items-center justify-center text-2xl">
                                🍱
                            </div>
                        @endif
                        <div class="flex-1 min-w-0">
                            <p class="font-medium text-sm text-base-content leading-snug">
                                {{ $item->surplusProduct->product->name ?? '-' }}
                            </p>
                            <p class="text-xs text-base-content/50 mt-0.5">
                                {{ $item->quantity }} × Rp {{ number_format($item->price, 0, ',', '.') }}
                            </p>
                            @if ($item->surplusProduct->product->category)
                                <span class="text-[10px] text-orange-500 font-medium">
                                    {{ $item->surplusProduct->product->category->name }}
                                </span>
                            @endif
                        </div>
                        <p class="font-bold text-sm text-base-content shrink-0">
                            Rp {{ number_format($item->subtotal(), 0, ',', '.') }}
                        </p>
                    </div>
                @endforeach

                {{-- Total --}}
                <div class="flex justify-between items-center px-4 py-3 bg-orange-50 border-t border-orange-100">
                    <span class="text-sm font-medium text-base-content/70">Total Pembayaran</span>
                    <span class="font-extrabold text-lg text-orange-500">
                        Rp {{ number_format($order->total_price, 0, ',', '.') }}
                    </span>
                </div>
            </div>

            {{-- ── INFO PEMBAYARAN ──────────────────────────────────────── --}}
            <div class="bg-base-100 rounded-2xl border border-base-200 shadow-sm p-4">
                <p class="text-xs text-base-content/40 uppercase tracking-wider mb-3">Info Pembayaran</p>
                <div class="space-y-2.5 text-sm">
                    <div class="flex justify-between">
                        <span class="text-base-content/50">ID Order</span>
                        <span class="font-mono text-base-content">#{{ $order->id }}</span>
                    </div>
                    @if ($order->payment_reference)
                        <div class="flex justify-between">
                            <span class="text-base-content/50">Referensi</span>
                            <span class="font-mono text-xs text-base-content">{{ $order->payment_reference }}</span>
                        </div>
                    @endif
                    @if ($order->paid_at)
                        <div class="flex justify-between">
                            <span class="text-base-content/50">Dibayar</span>
                            <span class="text-base-content">{{ $order->paid_at->format('d M Y, H:i') }}</span>
                        </div>
                    @endif
                    <div class="flex justify-between">
                        <span class="text-base-content/50">Dibuat</span>
                        <span class="text-base-content">{{ $order->created_at->format('d M Y, H:i') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-base-content/50">Status</span>
                        <span
                            class="font-semibold
                        {{ match ($order->status->value) {
                            'pending' => 'text-orange-500',
                            'paid' => 'text-blue-600',
                            'ready_for_pickup' => 'text-purple-600',
                            'completed' => 'text-green-600',
                            default => 'text-base-content/50',
                        } }}">
                            {{ $order->status->label() }}
                        </span>
                    </div>
                </div>
            </div>

            <a href="{{ route('user.orders.index') }}"
                class="block text-center text-sm text-base-content/40 hover:text-orange-500 transition-colors py-2">
                ← Kembali ke daftar pesanan
            </a>

        </div>
    </div>

    @push('scripts')
        <script>
            // ── Flash auto-hide ─────────────────────────────────────────
            setTimeout(() => document.getElementById('flashMsg')?.remove(), 5000);

            // ── Countdown timer ─────────────────────────────────────────
            const badge = document.getElementById('countdownBadge');
            if (badge) {
                const expiresAt = new Date(badge.dataset.expires);

                function tick() {
                    const diff = Math.max(0, expiresAt - new Date());
                    const m = Math.floor(diff / 60000);
                    const s = Math.floor((diff % 60000) / 1000);

                    badge.textContent = `${String(m).padStart(2,'0')}:${String(s).padStart(2,'0')}`;

                    if (diff === 0) {
                        // Waktu habis → reload untuk tampilkan state expired
                        window.location.reload();
                    }
                }

                tick();
                setInterval(tick, 1000);
            }

            // ── Midtrans Snap ───────────────────────────────────────────
            @if ($snapToken)
                const SNAP_TOKEN = @json($snapToken);

                function triggerSnap() {

                    if (!window.snap) {
                        console.error('Midtrans Snap belum loaded');
                        return;
                    }

                    const btn = document.getElementById('payBtn');

                    btn.disabled = true;

                    btn.innerHTML = `
        <span class="loading loading-spinner loading-sm"></span>
        Membuka Pembayaran...
    `;

                    window.snap.pay(SNAP_TOKEN, {
                        onSuccess: function(result) {
                            window.location.href = `/user/orders/{{ $order->id }}`;
                        },

                        onPending: function(result) {
                            window.location.href = `/user/orders/{{ $order->id }}`;
                        },

                        onError: function(result) {

                            btn.disabled = false;

                            btn.innerHTML = `Bayar Sekarang`;

                            showToast('Pembayaran gagal. Silahkan coba lagi.', 'error');
                        },

                        onClose: function() {

                            btn.disabled = false;

                            btn.innerHTML = `Bayar Sekarang`;
                        },
                    });
                }

                // Auto-open Snap jika baru redirect dari checkout
                @if ($order->isPending() && !$order->isPaymentExpired() && $snapToken)
                    window.addEventListener('load', () => {

                        const waitSnap = setInterval(() => {

                            if (window.snap && typeof window.snap.pay === 'function') {

                                clearInterval(waitSnap);

                                triggerSnap();
                            }

                        }, 300);

                    });
                @endif
            @endif

            // ── Toast helper ────────────────────────────────────────────
            function showToast(message, type = 'info') {
                const toast = document.createElement('div');
                toast.className = `
            fixed bottom-20 left-1/2 -translate-x-1/2 z-50
            px-4 py-2.5 rounded-xl text-white text-sm font-medium shadow-xl
            ${type === 'error' ? 'bg-red-500' : 'bg-green-500'}
            animate-bounce
        `;
                toast.textContent = message;
                document.body.appendChild(toast);
                setTimeout(() => toast.remove(), 4000);
            }
        </script>

        <style>
            @keyframes pulse-ring {
                0% {
                    transform: scale(1);
                    opacity: 0.7;
                }

                100% {
                    transform: scale(1.4);
                    opacity: 0;
                }
            }

            .tabular-nums {
                font-variant-numeric: tabular-nums;
            }
        </style>
    @endpush

@endsection
