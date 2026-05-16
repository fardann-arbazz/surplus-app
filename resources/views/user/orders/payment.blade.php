@extends('layouts.user')

@section('title', 'Pembayaran Order #' . $order->id)

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
    <div class="container py-10 max-w-lg mx-auto text-center">

        <h1 class="text-2xl font-semibold mb-2">Selesaikan Pembayaran</h1>
        <p class="text-gray-500 mb-6">
            Order <strong>#{{ $order->id }}</strong> —
            Total <strong>Rp {{ number_format($order->total_price, 0, ',', '.') }}</strong>
        </p>

        {{-- Countdown (hanya tampil jika expires_at masih di masa depan) --}}
        @if ($order->expires_at && $order->expires_at->isFuture())
            <p class="text-sm text-orange-500 mb-6">
                Bayar sebelum <strong>{{ $order->expires_at->format('H:i') }}</strong>
                ({{ $order->expires_at->diffForHumans() }})
            </p>
        @endif

        {{-- Tombol trigger Snap popup --}}
        <button id="pay-btn"
            class="px-8 py-3 bg-orange-600 text-white text-base font-semibold rounded-xl hover:bg-green-700 transition shadow-md">
            Bayar Sekarang
        </button>

        <p class="mt-4 text-xs text-gray-400">
            Pembayaran diproses dengan aman melalui Midtrans.
        </p>

        <a href="{{ route('user.orders.show', $order->id) }}" class="block mt-6 text-sm text-gray-400 hover:text-gray-600">
            ← Kembali ke detail pesanan
        </a>

    </div>

    @push('scripts')
        <script>
            const snapToken = @json($snapToken);

            document.getElementById('pay-btn').addEventListener('click', function() {
                window.snap.pay(snapToken, {
                    onSuccess: function(result) {
                        // Pembayaran berhasil, redirect ke detail order
                        window.location.href = '{{ route('user.orders.show', $order->id) }}';
                    },
                    onPending: function(result) {
                        // Menunggu pembayaran (misal transfer bank)
                        window.location.href = '{{ route('user.orders.show', $order->id) }}';
                    },
                    onError: function(result) {
                        // Pembayaran gagal
                        alert('Pembayaran gagal. Silahkan coba lagi atau pilih metode lain.');
                    },
                    onClose: function() {
                        // User menutup popup tanpa bayar
                        // Tidak redirect, biarkan user memilih bayar lagi
                    },
                });
            });

            // Auto-trigger popup langsung saat halaman dibuka (UX lebih mulus)
            // Hapus baris ini jika Anda ingin user klik tombol dulu
            window.addEventListener('load', function() {
                document.getElementById('pay-btn').click();
            });
        </script>
    @endpush

@endsection
