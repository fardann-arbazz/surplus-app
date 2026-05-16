<?php

namespace App\Services\Orders;

use App\Enums\OrderStatus;
use App\Exceptions\Order\InsufficientStockException;
use App\Exceptions\Order\InvalidStatusTransitionException;
use App\Exceptions\Order\MultiStoreOrderException;
use App\Exceptions\Order\OrderNotFoundException;
use App\Exceptions\Order\SurplusExpiredException;
use App\Models\OrderItems;
use App\Models\Orders;
use App\Models\SurplusProduct;
use App\Models\User;
use App\Services\Midtrans\MidtransService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderService
{
    public function __construct(
        private readonly MidtransService $midtrans,
    ) {}

    /* ══════════════════════════════════════════════════════════
     *  1. CHECKOUT  — user kirim keranjang → buat order + Midtrans
     *
     *  $items = [
     *    ['surplus_id' => 3, 'quantity' => 2],
     *    ['surplus_id' => 5, 'quantity' => 1],
     *  ]
     * ══════════════════════════════════════════════════════════ */
    public function checkout(User $user, array $items): array
    {
        return DB::transaction(function () use ($user, $items) {

            // ── 1a. Load semua surplus dengan LOCK (cegah race condition) ──
            $surplusIds = collect($items)->pluck('surplus_id')->unique();

            $surplusMap = SurplusProduct::with('product.store')
                ->whereIn('id', $surplusIds)
                ->lockForUpdate()   // ← SELECT ... FOR UPDATE
                ->get()
                ->keyBy('id');

            // ── 1b. Validasi: 1 order = 1 toko ──────────────────────────
            $storeIds = $surplusMap
                ->map(fn($s) => $s->product->store_id)
                ->unique();

            if ($storeIds->count() > 1) {
                throw new MultiStoreOrderException();
            }

            $store   = $surplusMap->first()->product->store;
            $storeId = $store->id;

            // ── 1c. Validasi stok & expiry per item ──────────────────────
            $orderLines = [];  // akan jadi OrderItem
            $totalPrice = 0;

            foreach ($items as $item) {
                $surplus = $surplusMap->get($item['surplus_id'])
                    ?? throw new OrderNotFoundException();

                // Sudah expired?
                if ($surplus->expired_at && now()->isAfter($surplus->expired_at)) {
                    throw new SurplusExpiredException($surplus->product->name);
                }

                // Stok cukup?
                if ($surplus->remaining_quantity < $item['quantity']) {
                    throw new InsufficientStockException(
                        $surplus->product->name,
                        $item['quantity'],
                        $surplus->remaining_quantity,
                    );
                }

                $linePrice = $surplus->discount_price * $item['quantity'];
                $totalPrice += $linePrice;

                $orderLines[] = [
                    'surplus'  => $surplus,
                    'quantity' => $item['quantity'],
                    'price'    => $surplus->discount_price,   // snapshot
                ];
            }

            // ── 1d. Kurangi stok (masih di dalam transaksi) ──────────────
            foreach ($orderLines as $line) {
                $line['surplus']->decrement('remaining_quantity', $line['quantity']);

                // Auto-update status surplus ke sold_out jika habis
                if ($line['surplus']->remaining_quantity === 0) {
                    $line['surplus']->update(['status' => 'sold_out']);
                }
            }

            // ── 1e. Buat Order ────────────────────────────────────────────
            $order = Orders::create([
                'user_id'    => $user->id,
                'store_id'   => $storeId,
                'total_price' => $totalPrice,
                'status'     => OrderStatus::Pending,
                'expires_at' => now()->addMinutes(15),   // deadline bayar
            ]);

            // ── 1f. Buat OrderItems ───────────────────────────────────────
            foreach ($orderLines as $line) {
                OrderItems::create([
                    'order_id'  => $order->id,
                    'surplus_id' => $line['surplus']->id,
                    'quantity'  => $line['quantity'],
                    'price'     => $line['price'],
                ]);
            }

            // ── 1g. Buat Midtrans Snap token ──────────────────────────────
            $snapToken = $this->midtrans->createSnapToken($order, $user);

            $order->update(['snap_token' => $snapToken]);

            return [
                'order'      => $order->load('orderItems.surplusProduct.product'),
                'snap_token' => $snapToken,
                'expires_at' => $order->expires_at->toIso8601String(),
            ];
        });
    }

    /* ══════════════════════════════════════════════════════════
     *  2. MIDTRANS WEBHOOK  — terima notifikasi pembayaran
     *
     *  Dipanggil dari MidtransWebhookController setelah
     *  signature verified.
     * ══════════════════════════════════════════════════════════ */
    public function handlePaymentNotification(array $payload): void
    {
        \Log::info('MIDTRANS WEBHOOK MASUK');
        $orderId = (int) str_replace('ORDER-', '', $payload['order_id']);
        $order   = Orders::where('id', $orderId)->firstOrFail();

        $transactionStatus = $payload['transaction_status'];
        $fraudStatus       = $payload['fraud_status'] ?? 'accept';

        $isPaid = in_array($transactionStatus, ['capture', 'settlement'])
            && $fraudStatus === 'accept';

        $isExpired = in_array($transactionStatus, ['cancel', 'deny', 'expire']);

        DB::transaction(function () use ($order, $isPaid, $isExpired, $payload) {
            if ($isPaid && $order->status === OrderStatus::Pending) {
                $this->transitionStatus($order, OrderStatus::Paid);
                $order->update([
                    'payment_reference' => $payload['transaction_id'],
                    'paid_at'           => now(),
                    'pickup_code'       => $this->generatePickupCode(),
                ]);
            }

            if ($isExpired && $order->status === OrderStatus::Pending) {
                $this->transitionStatus($order, OrderStatus::Expired);
                $this->restoreStock($order);
            }
        });
    }

    /* ══════════════════════════════════════════════════════════
     *  3. SELLER: konfirmasi pesanan siap diambil
     * ══════════════════════════════════════════════════════════ */
    public function markReadyForPickup(Orders $order, User $seller): Orders
    {
        // Pastikan order milik toko si seller
        $this->assertSellerOwnsOrder($seller, $order);

        $this->transitionStatus($order, OrderStatus::ReadyForPickup);

        return $order->fresh();
    }

    /* ══════════════════════════════════════════════════════════
     *  4. USER: konfirmasi sudah pickup
     *
     *  User wajib input pickup_code yang diberikan seller
     *  saat proses serah terima → cegah klaim palsu.
     * ══════════════════════════════════════════════════════════ */
    public function confirmPickup(Orders $order, User $user, string $pickupCode): Orders
    {
        // Pastikan order milik user
        if ($order->user_id !== $user->id) {
            throw new OrderNotFoundException();
        }

        // Verifikasi kode pickup (case-insensitive)
        if (strtoupper($pickupCode) !== strtoupper($order->pickup_code)) {
            throw new \InvalidArgumentException('Kode pickup tidak sesuai.');
        }

        $this->transitionStatus($order, OrderStatus::Completed);

        return $order->fresh();
    }

    /* ══════════════════════════════════════════════════════════
     *  5. USER: list order miliknya (dengan filter status)
     * ══════════════════════════════════════════════════════════ */
    public function getUserOrders(User $user, ?string $status = null)
    {
        return Orders::with(['orderItems.surplusProduct.product.productImg', 'stores'])
            ->where('user_id', $user->id)
            ->when($status, fn($q) => $q->where('status', $status))
            ->latest()
            ->paginate(10);
    }

    /* ══════════════════════════════════════════════════════════
     *  6. SELLER: list order masuk ke tokonya
     * ══════════════════════════════════════════════════════════ */
    public function getSellerOrders(User $seller, ?string $status = null)
    {
        // Ambil semua store milik seller
        $storeIds = $seller->stores()->pluck('id');

        return Orders::with(['orderItems.surplusProduct.product', 'user:id,name,phone'])
            ->whereIn('store_id', $storeIds)
            ->when($status, fn($q) => $q->where('status', $status))
            ->latest()
            ->paginate(10);
    }

    /* ══════════════════════════════════════════════════════════
     *  7. Detail order (user & seller sama-sama pakai)
     * ══════════════════════════════════════════════════════════ */
    public function getOrderDetail(int $orderId, User $actor): Orders
    {
        $order = Orders::with([
            'orderItems.surplusProduct.product.productImg',
            'orderItems.surplusProduct.product.category',
            'stores',
            'user:id,name,phone',
        ])->find($orderId);

        if (! $order) {
            throw new OrderNotFoundException();
        }

        // Boleh lihat jika: pemilik order, atau seller toko tersebut
        $isOwner  = $order->user_id === $actor->id;
        $isSeller = $actor->stores()->where('id', $order->store_id)->exists();

        if (! $isOwner && ! $isSeller) {
            throw new OrderNotFoundException();
        }

        return $order;
    }

    /* ══════════════════════════════════════════════════════════
     *  PRIVATE HELPERS
     * ══════════════════════════════════════════════════════════ */

    private function transitionStatus(Orders $order, OrderStatus $next): void
    {
        if (! $order->status->canTransitionTo($next)) {
            throw new InvalidStatusTransitionException(
                $order->status->value,
                $next->value,
            );
        }

        $order->update(['status' => $next]);
    }

    /** Kembalikan stok surplus ketika order expired/cancel */
    private function restoreStock(Orders $order): void
    {
        foreach ($order->orderItems as $item) {
            $item->surplus()->increment('remaining_quantity', $item->quantity);

            // Kalau sebelumnya sold_out, kembalikan ke active (jika belum expired)
            $surplus = $item->surplus;
            if ($surplus->status === 'sold_out' && now()->isBefore($surplus->expired_at)) {
                $surplus->update(['status' => 'active']);
            }
        }
    }

    private function assertSellerOwnsOrder(User $seller, Orders $order): void
    {
        $owns = $seller->stores()->where('id', $order->store_id)->exists();
        if (! $owns) {
            throw new OrderNotFoundException();
        }
    }

    private function generatePickupCode(): string
    {
        do {
            $code = strtoupper(Str::random(6));
        } while (Orders::where('pickup_code', $code)->exists());

        return $code;
    }
}
