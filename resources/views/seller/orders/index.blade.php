@extends('layouts.seller')

@section('title', 'Pesanan Masuk')

@section('content')
    <div class="container py-6">

        <h1 class="text-2xl font-semibold mb-4">Pesanan Masuk</h1>

        {{-- ── Filter Status ─────────────────────────────────────── --}}
        <div class="flex flex-wrap gap-2 mb-6">
            @php
                $statuses = [
                    '' => 'Semua',
                    'paid' => 'Sudah Dibayar',
                    'ready_for_pickup' => 'Siap Diambil',
                    'completed' => 'Selesai',
                    'expired' => 'Kadaluarsa',
                ];
            @endphp

            @foreach ($statuses as $value => $label)
                <a href="{{ route('seller.orders.index', $value ? ['status' => $value] : []) }}"
                    class="px-4 py-1.5 rounded-full border text-sm transition
                      {{ $status === $value ? 'bg-orange-600 text-white border-orange-600' : 'border-gray-300 text-gray-600 hover:border-orange-500' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>

        {{-- Flash --}}
        @if (session('success'))
            <div class="mb-4 p-3 bg-green-50 border border-green-200 text-green-700 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        @forelse ($orders as $order)
            <div class="bg-white border border-gray-200 rounded-xl mb-4 overflow-hidden shadow-sm">

                <div class="flex items-center justify-between px-4 py-3 bg-gray-50 border-b border-gray-100">
                    <div>
                        <span class="font-medium text-gray-800">Order #{{ $order->id }}</span>
                        <span class="text-xs text-gray-400 ml-2">
                            {{ $order->created_at->format('d M Y, H:i') }}
                        </span>
                    </div>
                    <span
                        class="text-xs font-semibold px-2.5 py-1 rounded-full
                    {{ match ($order->status->value) {
                        'paid' => 'bg-blue-100 text-blue-700',
                        'ready_for_pickup' => 'bg-orange-100 text-orange-700',
                        'completed' => 'bg-green-100 text-green-700',
                        default => 'bg-gray-100 text-gray-500',
                    } }}">
                        {{ $order->status->label() }}
                    </span>
                </div>

                <div class="px-4 py-3">
                    {{-- Info pembeli --}}
                    <p class="text-sm text-gray-600 mb-2">
                        <strong>{{ $order->user->name ?? '-' }}</strong>
                        @if ($order->user->phone)
                            — {{ $order->user->phone }}
                        @endif
                    </p>

                    {{-- Preview produk --}}
                    @foreach ($order->orderItems->take(2) as $item)
                        <p class="text-sm text-gray-700">
                            • {{ $item->surplusProduct->product->name ?? '-' }}
                            <span class="text-gray-400">×{{ $item->quantity }}</span>
                        </p>
                    @endforeach
                    @if ($order->orderItems->count() > 2)
                        <p class="text-xs text-gray-400">+{{ $order->orderItems->count() - 2 }} lainnya</p>
                    @endif
                </div>

                <div class="flex items-center justify-between px-4 py-3 border-t border-gray-100">
                    <p class="font-semibold text-gray-900">
                        Rp {{ number_format($order->total_price, 0, ',', '.') }}
                    </p>
                    <a href="{{ route('seller.orders.show', $order->id) }}"
                        class="text-sm px-4 py-1.5 rounded-lg border border-gray-300 text-gray-600 hover:bg-gray-50 transition">
                        Lihat Detail
                    </a>
                </div>
            </div>
        @empty
            <div class="text-center py-16 text-gray-400">
                <p class="text-lg">Belum ada pesanan masuk</p>
            </div>
        @endforelse

        <div class="mt-4">
            {{ $orders->links() }}
        </div>

    </div>
@endsection
