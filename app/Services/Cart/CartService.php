<?php

namespace App\Services\Cart;

use App\Models\Cart;
use App\Models\SurplusProduct;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class CartService
{
    /**
     * Ambil semua item cart milik user beserta relasi lengkap.
     */
    public function getCart(User $user): Collection
    {
        return Cart::with([
            'surplus.product.productImg',
            'surplus.product.category',
            'surplus.product.store',
        ])
            ->where('user_id', $user->id)
            ->latest()
            ->get();
    }

    /**
     * Hitung total harga seluruh cart.
     */
    public function getTotal(Collection $cartItems): float
    {
        return $cartItems->sum(fn(Cart $item) => $item->subtotal());
    }

    /**
     * Hitung jumlah item (quantity) di cart — untuk badge topbar.
     */
    public function getCount(User $user): int
    {
        return Cart::where('user_id', $user->id)->sum('quantity');
    }

    /**
     * Tambah surplus ke cart.
     * Jika sudah ada, tambahkan quantitynya.
     *
     * @throws \InvalidArgumentException  jika stok tidak cukup
     * @throws \RuntimeException          jika surplus sudah expired
     */
    public function addItem(User $user, int $surplusId, int $quantity = 1): Cart
    {
        $surplus = SurplusProduct::with('product')->findOrFail($surplusId);

        // Validasi expired
        if ($surplus->expired_at && $surplus->expired_at->isPast()) {
            throw new \RuntimeException("Surplus '{$surplus->product->name}' sudah kadaluarsa.");
        }

        // Cek existing cart item
        $existing = Cart::where('user_id', $user->id)
            ->where('surplus_id', $surplusId)
            ->first();

        $newQty = ($existing?->quantity ?? 0) + $quantity;

        // Validasi stok
        if ($newQty > $surplus->remaining_quantity) {
            throw new \InvalidArgumentException(
                "Stok tidak cukup. Tersedia: {$surplus->remaining_quantity}."
            );
        }

        return Cart::updateOrCreate(
            ['user_id' => $user->id, 'surplus_id' => $surplusId],
            ['quantity' => $newQty]
        );
    }

    /**
     * Update quantity sebuah item cart.
     *
     * @throws \InvalidArgumentException  jika stok tidak cukup
     */
    public function updateQuantity(User $user, int $cartId, int $quantity): Cart
    {
        $cart    = $this->findOwned($user, $cartId);
        $surplus = $cart->surplus;

        if ($quantity < 1) {
            throw new \InvalidArgumentException('Quantity minimal 1.');
        }

        if ($quantity > $surplus->remaining_quantity) {
            throw new \InvalidArgumentException(
                "Stok tidak cukup. Tersedia: {$surplus->remaining_quantity}."
            );
        }

        $cart->update(['quantity' => $quantity]);

        return $cart->fresh();
    }

    /**
     * Hapus satu item dari cart.
     */
    public function removeItem(User $user, int $cartId): void
    {
        $this->findOwned($user, $cartId)->delete();
    }

    /**
     * Kosongkan seluruh cart user.
     */
    public function clearCart(User $user): void
    {
        Cart::where('user_id', $user->id)->delete();
    }

    /**
     * Konversi cart ke format yang dibutuhkan CheckoutRequest.
     * Digunakan saat user klik "Checkout" dari halaman cart.
     */
    public function toCheckoutItems(Collection $cartItems): array
    {
        return $cartItems->map(fn(Cart $item) => [
            'surplus_id' => $item->surplus_id,
            'quantity'   => $item->quantity,
        ])->values()->all();
    }

    /* ── Private Helpers ────────────────────────────────────── */

    private function findOwned(User $user, int $cartId): Cart
    {
        $cart = Cart::with('surplus.product')->find($cartId);

        if (! $cart || $cart->user_id !== $user->id) {
            throw new \InvalidArgumentException('Item cart tidak ditemukan.');
        }

        return $cart;
    }
}
