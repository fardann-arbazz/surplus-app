@extends('layouts.user')

@section('title', 'Review Pesanan')

@section('content')
    <div class="container py-6 max-w-xl mx-auto">

        <h1 class="text-2xl font-semibold mb-6">Review Pesanan</h1>

        {{-- Errors --}}
        @if ($errors->any())
            <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('error'))
            <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">
                {{ session('error') }}
            </div>
        @endif

        {{-- Form checkout --}}
        <form action="{{ route('user.orders.checkout.process') }}" method="POST">
            @csrf

            <div class="bg-white border border-gray-200 rounded-xl overflow-hidden mb-5 shadow-sm">
                <p class="px-4 py-3 font-medium text-gray-700 border-b border-gray-100">Item Pesanan</p>

                @php $grandTotal = 0; @endphp

                @foreach ($cartItems as $index => $item)
                    {{-- Hidden inputs untuk checkout --}}
                    <input type="hidden" name="items[{{ $index }}][surplus_id]" value="{{ $item['surplus_id'] }}">
                    <input type="hidden" name="items[{{ $index }}][quantity]" value="{{ $item['quantity'] }}">

                    <div class="flex items-center gap-3 px-4 py-3 border-b border-gray-50 last:border-0">
                        {{-- Tampilkan detail produk jika tersimpan di cart --}}
                        @if (isset($item['product_image']))
                            <img src="{{ asset('storage/' . $item['product_image']) }}"
                                class="w-14 h-14 rounded-lg object-cover shrink-0" alt="">
                        @else
                            <div
                                class="w-14 h-14 rounded-lg bg-gray-100 shrink-0 flex items-center justify-center text-gray-300 text-xl">
                                📦
                            </div>
                        @endif
                        <div class="flex-1">
                            <p class="font-medium text-gray-800">
                                {{ $item['product_name'] ?? 'Produk #' . $item['surplus_id'] }}
                            </p>
                            <p class="text-sm text-gray-500">
                                {{ $item['quantity'] }} × Rp {{ number_format($item['price'] ?? 0, 0, ',', '.') }}
                            </p>
                        </div>
                        @php
                            $subtotal = ($item['price'] ?? 0) * $item['quantity'];
                            $grandTotal += $subtotal;
                        @endphp
                        <p class="font-semibold text-gray-800 text-sm">
                            Rp {{ number_format($subtotal, 0, ',', '.') }}
                        </p>
                    </div>
                @endforeach

                {{-- Total --}}
                <div class="flex justify-between items-center px-4 py-3 bg-gray-50 border-t border-gray-100">
                    <span class="text-gray-600 font-medium">Total Pembayaran</span>
                    <span class="font-bold text-gray-900 text-xl">
                        Rp {{ number_format($grandTotal, 0, ',', '.') }}
                    </span>
                </div>
            </div>

            <button type="submit"
                class="w-full py-3 bg-orange-600 text-white font-semibold text-base rounded-xl
                       hover:bg-orange-700 transition shadow-md">
                Buat Pesanan & Bayar
            </button>
        </form>

        <a href="#" class="block text-center mt-4 text-sm text-gray-400 hover:text-gray-600">
            ← Kembali ke keranjang
        </a>

    </div>
@endsection
