@extends('layouts.seller')

@section('title', 'Detail Pesanan #' . $order->id)

@section('content')
    <div class="container py-6 max-w-2xl mx-auto">

        {{-- Breadcrumb --}}
        <div class="text-sm text-gray-500 mb-4">
            <a href="{{ route('seller.orders.index') }}" class="hover:text-indigo-600">Pesanan Masuk</a>
            <span class="mx-2">/</span>
            <span>Order #{{ $order->id }}</span>
        </div>

        {{-- Flash --}}
        @if (session('success'))
            <div class="mb-4 p-3 bg-green-50 border border-green-200 text-green-700 rounded-lg">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 rounded-lg">
                {{ session('error') }}
            </div>
        @endif

        {{-- Pickup Code Banner  --}}
        @if (session('pickup_code'))
            <div class="mb-5 p-5 bg-orange-50 border-2 border-orange-400 rounded-xl text-center">
                <p class="text-sm font-medium text-orange-700 mb-1">
                    ✅ Pesanan siap diambil! Tunjukkan kode ini ke pembeli:
                </p>
                <p class="text-4xl font-mono font-bold tracking-widest text-orange-800 my-2">
                    {{ session('pickup_code') }}
                </p>
                <p class="text-xs text-orange-500">
                    Kode ini juga tersimpan di detail order dan dapat dilihat kembali di bawah.
                </p>
            </div>
        @endif

        {{-- ── Status Banner ──────────────────────────────────────── --}}
        <div
            class="rounded-xl p-4 mb-5 flex items-center justify-between
        {{ match ($order->status->value) {
            'paid' => 'bg-blue-50 border border-blue-200',
            'ready_for_pickup' => 'bg-orange-50 border border-orange-200',
            'completed' => 'bg-green-50 border border-green-200',
            default => 'bg-gray-50 border border-gray-200',
        } }}">
            <div>
                <p class="text-xs text-gray-400">Status Pesanan</p>
                <p
                    class="font-semibold
                {{ match ($order->status->value) {
                    'paid' => 'text-blue-700',
                    'ready_for_pickup' => 'text-orange-700',
                    'completed' => 'text-green-700',
                    default => 'text-gray-600',
                } }}">
                    {{ $order->status->label() }}
                </p>
            </div>

            {{-- Tombol "Siap Diambil" — hanya muncul jika status = paid --}}
            @if ($order->status->value === 'paid')
                <form action="{{ route('seller.orders.ready', $order->id) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <button type="submit" onclick="return confirm('Konfirmasi pesanan ini siap diambil?')"
                        class="px-5 py-2 bg-blue-600 text-white text-sm font-semibold
                               rounded-lg hover:bg-blue-700 transition shadow-sm">
                        Tandai Siap Diambil
                    </button>
                </form>
            @endif

            {{-- Tampilkan pickup code jika sudah ready_for_pickup --}}
            @if ($order->status->value === 'ready_for_pickup' && $order->pickup_code)
                <div class="text-right">
                    <p class="text-xs text-orange-500 mb-0.5">Kode Pickup</p>
                    <p class="text-2xl font-mono font-bold tracking-widest text-orange-800">
                        {{ $order->pickup_code }}
                    </p>
                </div>
            @endif
        </div>

        {{-- ── Info Pembeli ────────────────────────────────────────── --}}
        <div class="bg-white border border-gray-200 rounded-xl p-4 mb-4 shadow-sm">
            <p class="text-xs text-gray-400 mb-2">Data Pembeli</p>
            <div class="flex items-center gap-3">
                <div
                    class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-semibold text-sm">
                    {{ strtoupper(substr($order->user->name ?? 'U', 0, 1)) }}
                </div>
                <div>
                    <p class="font-medium text-gray-800">{{ $order->user->name ?? '-' }}</p>
                    @if ($order->user->phone)
                        <p class="text-sm text-gray-500">{{ $order->user->phone }}</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- ── Daftar Produk ──────────────────────────────────────── --}}
        <div class="bg-white border border-gray-200 rounded-xl overflow-hidden mb-4 shadow-sm">
            <p class="px-4 py-3 font-medium text-gray-700 border-b border-gray-100">Produk Dipesan</p>

            @foreach ($order->orderItems as $item)
                <div class="flex items-center gap-3 px-4 py-3 border-b border-gray-50 last:border-0">
                    @php $img = $item->surplusProduct->product->productImg->first(); @endphp
                    @if ($img)
                        <img src="{{ $img->img_url }}" class="w-14 h-14 rounded-lg object-cover shrink-0" alt="">
                    @else
                        <div class="w-14 h-14 rounded-lg bg-gray-100 shrink-0"></div>
                    @endif
                    <div class="flex-1">
                        <p class="font-medium text-gray-800">
                            {{ $item->surplusProduct->product->name ?? '-' }}
                        </p>
                        <p class="text-sm text-gray-500">
                            {{ $item->quantity }} × Rp {{ number_format($item->price, 0, ',', '.') }}
                        </p>
                    </div>
                    <p class="font-semibold text-gray-800 text-sm">
                        Rp {{ number_format($item->subtotal(), 0, ',', '.') }}
                    </p>
                </div>
            @endforeach

            <div class="flex justify-between items-center px-4 py-3 bg-gray-50 border-t border-gray-100">
                <span class="text-gray-600">Total</span>
                <span class="font-bold text-gray-900 text-lg">
                    Rp {{ number_format($order->total_price, 0, ',', '.') }}
                </span>
            </div>
        </div>

        {{-- ── Info Pembayaran ─────────────────────────────────────── --}}
        <div class="bg-white border border-gray-200 rounded-xl p-4 mb-4 shadow-sm">
            <p class="text-xs text-gray-400 mb-3">Info Pembayaran</p>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-500">ID Order</span>
                    <span class="font-mono text-gray-700">#{{ $order->id }}</span>
                </div>
                @if ($order->payment_reference)
                    <div class="flex justify-between">
                        <span class="text-gray-500">Referensi Midtrans</span>
                        <span class="font-mono text-gray-700 text-xs">{{ $order->payment_reference }}</span>
                    </div>
                @endif
                @if ($order->paid_at)
                    <div class="flex justify-between">
                        <span class="text-gray-500">Dibayar</span>
                        <span class="text-gray-700">{{ $order->paid_at->format('d M Y, H:i') }}</span>
                    </div>
                @endif
                <div class="flex justify-between">
                    <span class="text-gray-500">Tanggal Order</span>
                    <span class="text-gray-700">{{ $order->created_at->format('d M Y, H:i') }}</span>
                </div>
            </div>
        </div>

        <a href="{{ route('seller.orders.index') }}"
            class="block text-center text-sm text-gray-500 hover:text-orange-600 mt-2">
            ← Kembali ke daftar pesanan
        </a>

    </div>
@endsection
