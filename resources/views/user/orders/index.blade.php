@extends('layouts.user')

@section('title', 'Pesanan Saya')

@section('content')
    <div class="container py-6">

        <h1 class="text-2xl font-semibold mb-4">Pesanan Saya</h1>

        {{-- ── Filter Status ─────────────────────────────────────── --}}
        <div class="flex flex-wrap gap-2 mb-6">
            @php
                $statuses = [
                    '' => 'Semua',
                    'pending' => 'Menunggu Bayar',
                    'paid' => 'Sudah Bayar',
                    'ready_for_pickup' => 'Siap Diambil',
                    'completed' => 'Selesai',
                    'expired' => 'Kadaluarsa',
                ];
            @endphp

            @foreach ($statuses as $value => $label)
                <a href="{{ route('user.orders.index', $value ? ['status' => $value] : []) }}"
                    class="px-4 py-1.5 rounded-full border text-sm transition
                      {{ $status === $value ? 'bg-orange-600 text-white border-orange-600' : 'border-gray-300 text-gray-600 hover:border-orange-500' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>

        {{-- ── Flash Messages ────────────────────────────────────── --}}
        @if (session('success'))
            <div class="mb-4 p-3 bg-green-50 border border-green-200 text-green-700 rounded">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 rounded">
                {{ session('error') }}
            </div>
        @endif

        {{-- ── Daftar Order ──────────────────────────────────────── --}}
        @forelse ($orders as $order)
            <div class="bg-white border border-gray-200 rounded-xl mb-4 overflow-hidden shadow-sm">

                {{-- Header: toko + status --}}
                <div class="flex items-center justify-between px-4 py-3 bg-gray-50 border-b border-gray-100">
                    <span class="font-medium text-gray-800">
                        🏪 {{ $order->stores->name ?? '-' }}
                    </span>
                    <span
                        class="text-xs font-semibold px-2.5 py-1 rounded-full
                    {{ match ($order->status->value) {
                        'pending' => 'bg-yellow-100 text-yellow-700',
                        'paid' => 'bg-blue-100 text-blue-700',
                        'ready_for_pickup' => 'bg-purple-100 text-purple-700',
                        'completed' => 'bg-green-100 text-green-700',
                        default => 'bg-gray-100 text-gray-500',
                    } }}">
                        {{ $order->status->label() }}
                    </span>
                </div>

                {{-- Item produk (tampil max 2, sisanya "+N lainnya") --}}
                <div class="px-4 py-3">
                    @foreach ($order->orderItems->take(2) as $item)
                        <div class="flex items-center gap-3 py-1.5">
                            @php $img = $item->surplusProduct->product->productImg->first(); @endphp
                            @if ($img)
                                <img src="{{ $img->img_url }}" class="w-12 h-12 rounded-lg object-cover shrink-0"
                                    alt="">
                            @else
                                <div class="w-12 h-12 rounded-lg bg-gray-100 shrink-0"></div>
                            @endif
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-800 truncate">
                                    {{ $item->surplusProduct->product->name ?? '-' }}
                                </p>
                                <p class="text-xs text-gray-500">
                                    {{ $item->quantity }} × Rp {{ number_format($item->price, 0, ',', '.') }}
                                </p>
                            </div>
                        </div>
                    @endforeach

                    @if ($order->orderItems->count() > 2)
                        <p class="text-xs text-gray-400 mt-1">
                            +{{ $order->orderItems->count() - 2 }} produk lainnya
                        </p>
                    @endif
                </div>

                {{-- Footer: total + aksi --}}
                <div class="flex items-center justify-between px-4 py-3 border-t border-gray-100">
                    <div>
                        <p class="text-xs text-gray-400">Total Pembayaran</p>
                        <p class="font-semibold text-gray-900">
                            Rp {{ number_format($order->total_price, 0, ',', '.') }}
                        </p>
                    </div>
                    <div class="flex gap-2">
                        {{-- Tombol bayar jika masih pending --}}
                        @if ($order->isPending() && !$order->isPaymentExpired())
                            {{-- Hanya muncul jika snap_token masih tersedia di session
                             (redirect langsung dari checkout).
                             Untuk re-pay, perlu generate token baru — bisa ditambahkan nanti. --}}
                            <a href="{{ route('user.orders.show', $order->id) }}"
                                class="text-sm px-3 py-1.5 rounded-lg bg-orange-500 text-white hover:bg-orange-600 transition">
                                Bayar Sekarang
                            </a>
                        @endif

                        {{-- Tombol konfirmasi pickup --}}
                        @if ($order->status->value === 'ready_for_pickup')
                            <a href="{{ route('user.orders.confirm-pickup', $order->id) }}"
                                class="text-sm px-3 py-1.5 rounded-lg bg-green-600 text-white hover:bg-green-700 transition">
                                Konfirmasi Ambil
                            </a>
                        @endif

                        <a href="{{ route('user.orders.show', $order->id) }}"
                            class="text-sm px-3 py-1.5 rounded-lg border border-gray-300 text-gray-600 hover:bg-gray-50 transition">
                            Detail
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-16 text-gray-400">
                <p class="text-lg">Belum ada pesanan</p>
                <a href="{{ route('user.surplus-menu') }}" class="mt-3 inline-block text-sm text-orange-600 underline">
                    Lihat produk surplus
                </a>
            </div>
        @endforelse

        {{-- Pagination --}}
        <div class="mt-4">
            {{ $orders->links() }}
        </div>

    </div>
@endsection
