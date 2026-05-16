<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cart\AddToCartRequest;
use App\Http\Requests\Cart\UpdateCartRequest;
use App\Services\Cart\CartService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(private readonly CartService $cartService) {}

    /* ──────────────────────────────────────────────────────────
     * GET /user/cart
     * Tampilkan halaman cart
     * ────────────────────────────────────────────────────────── */
    public function index(Request $request)
    {
        $cartItems = $this->cartService->getCart($request->user());
        $total     = $this->cartService->getTotal($cartItems);

        // Kelompokkan per toko untuk tampilan dan validasi multi-store
        $grouped = $cartItems->groupBy(
            fn($item) => $item->surplus->product->store->id ?? 0
        );

        return view('user.cart.index', compact('cartItems', 'total', 'grouped'));
    }

    /* ──────────────────────────────────────────────────────────
     * POST /user/cart
     * Tambah item ke cart — dipanggil dari halaman product detail
     * ────────────────────────────────────────────────────────── */
    public function store(AddToCartRequest $request)
    {
        try {
            $this->cartService->addItem(
                user: $request->user(),
                surplusId: $request->validated('surplus_id'),
                quantity: $request->validated('quantity'),
            );

            return redirect()->back()
                ->with('success', 'Produk berhasil ditambahkan ke keranjang! 🛒');
        } catch (\RuntimeException $e) {
            // Surplus expired
            return redirect()->back()->with('error', $e->getMessage());
        } catch (\InvalidArgumentException $e) {
            // Stok tidak cukup
            return redirect()->back()->withErrors(['quantity' => $e->getMessage()]);
        }
    }

    /* ──────────────────────────────────────────────────────────
     * PATCH /user/cart/{cartId}
     * Update quantity — dikirim via form di cart page
     * ────────────────────────────────────────────────────────── */
    public function update(UpdateCartRequest $request, int $cartId)
    {
        try {
            $this->cartService->updateQuantity(
                user: $request->user(),
                cartId: $cartId,
                quantity: $request->validated('quantity'),
            );

            return redirect()->route('user.cart.index')
                ->with('success', 'Keranjang diperbarui.');
        } catch (\InvalidArgumentException $e) {
            return redirect()->route('user.cart.index')
                ->with('error', $e->getMessage());
        }
    }

    /* ──────────────────────────────────────────────────────────
     * DELETE /user/cart/{cartId}
     * Hapus satu item dari cart
     * ────────────────────────────────────────────────────────── */
    public function destroy(Request $request, int $cartId)
    {
        try {
            $this->cartService->removeItem($request->user(), $cartId);
        } catch (\InvalidArgumentException) {
            // Item tidak ditemukan / bukan milik user — abaikan saja
        }

        return redirect()->route('user.cart.index')
            ->with('success', 'Item dihapus dari keranjang.');
    }

    /* ──────────────────────────────────────────────────────────
     * DELETE /user/cart
     * Kosongkan seluruh cart
     * ────────────────────────────────────────────────────────── */
    public function clear(Request $request)
    {
        $this->cartService->clearCart($request->user());

        return redirect()->route('user.cart.index')
            ->with('success', 'Keranjang dikosongkan.');
    }

    /* ──────────────────────────────────────────────────────────
     * POST /user/cart/checkout
     * Pindahkan cart ke checkout:
     *   1. Simpan items ke session (format yang dibutuhkan CheckoutRequest)
     *   2. Redirect ke halaman checkout order
     * ────────────────────────────────────────────────────────── */
    public function proceedToCheckout(Request $request)
    {
        $cartItems = $this->cartService->getCart($request->user());

        if ($cartItems->isEmpty()) {
            return redirect()->route('user.cart.index')
                ->with('error', 'Keranjang kamu kosong.');
        }

        // Validasi semua item dari 1 toko
        $storeIds = $cartItems->pluck('surplus.product.store_id')->unique();
        if ($storeIds->count() > 1) {
            return redirect()->route('user.cart.index')
                ->with('error', 'Semua item harus dari toko yang sama untuk bisa checkout.');
        }

        // Simpan ke session dalam format CheckoutRequest
        $this->cartService->toCheckoutItems($cartItems);
        session(['cart' => $cartItems->map(fn($i) => [
            'surplus_id'   => $i->surplus_id,
            'quantity'     => $i->quantity,
            'product_name' => $i->surplus->product->name ?? '-',
            'product_image' => $i->surplus->product->productImg->first()?->img_url,
            'price'        => $i->surplus->discount_price ?? $i->surplus->product->price ?? 0,
        ])->values()->all()]);

        return redirect()->route('user.orders.checkout');
    }
}
