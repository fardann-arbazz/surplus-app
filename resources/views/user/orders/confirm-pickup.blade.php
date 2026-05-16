@extends('layouts.user')

@section('title', 'Konfirmasi Pickup #' . $order->id)

@section('content')
    <div class="container py-10 max-w-md mx-auto">

        <div class="text-center mb-8">
            <div class="text-5xl mb-3">📦</div>
            <h1 class="text-2xl font-semibold text-gray-800">Konfirmasi Pengambilan</h1>
            <p class="text-gray-500 mt-1 text-sm">
                Masukkan kode 6 karakter yang diberikan seller saat serah terima.
            </p>
        </div>

        {{-- Info order --}}
        <div class="bg-gray-50 border border-gray-200 rounded-xl p-4 mb-6 text-sm text-gray-700">
            <div class="flex justify-between mb-1">
                <span class="text-gray-400">Order</span>
                <span>#{{ $order->id }}</span>
            </div>
            <div class="flex justify-between mb-1">
                <span class="text-gray-400">Toko</span>
                <span>{{ $order->store->name ?? '-' }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-400">Total</span>
                <span class="font-semibold">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
            </div>
        </div>

        {{-- Error --}}
        @if ($errors->any())
            <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">
                {{ $errors->first('pickup_code') }}
            </div>
        @endif

        {{-- Form input kode pickup --}}
        <form action="{{ route('user.orders.confirm-pickup.process', $order->id) }}" method="POST">
            @csrf

            <div class="mb-5">
                <label for="pickup_code" class="block text-sm font-medium text-gray-700 mb-2">
                    Kode Pickup
                </label>
                <input type="text" id="pickup_code" name="pickup_code" maxlength="6" placeholder="Contoh: A1B2C3"
                    autocomplete="off" value="{{ old('pickup_code') }}"
                    class="w-full text-center text-2xl font-mono tracking-widest uppercase
                          border rounded-xl px-4 py-3 focus:outline-none focus:ring-2
                          focus:ring-green-500 focus:border-transparent
                          @error('pickup_code') border-red-400 @else @enderror">

                <p class="text-xs text-gray-400 text-center mt-2">
                    Kode tidak case-sensitive (huruf besar/kecil tidak dipermasalahkan)
                </p>
            </div>

            <button type="submit"
                class="w-full py-3 bg-green-600 text-white font-semibold rounded-xl
                       hover:bg-green-700 transition shadow-sm">
                Konfirmasi Pickup
            </button>
        </form>

        <a href="{{ route('user.orders.show', $order->id) }}"
            class="block text-center mt-4 text-sm text-gray-400 hover:text-gray-600">
            ← Kembali ke detail pesanan
        </a>

    </div>

    @push('scripts')
        <script>
            // Auto uppercase input kode
            document.getElementById('pickup_code').addEventListener('input', function() {
                this.value = this.value.toUpperCase();
            });
        </script>
    @endpush

@endsection
