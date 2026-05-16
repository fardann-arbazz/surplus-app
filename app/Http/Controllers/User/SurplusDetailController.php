<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\SurplusProduct;
use App\Services\Cart\CartService;
use Illuminate\Http\Request;

class SurplusDetailController extends Controller
{
    public function __construct(private readonly CartService $cartService) {}

    /* ──────────────────────────────────────────────────────────
     * GET /surplus/{id}
     * Halaman detail produk surplus
     * ────────────────────────────────────────────────────────── */
    public function show(Request $request, int $id)
    {
        $surplus = SurplusProduct::with([
            'product.productImg',
            'product.category',
            'product.store',
        ])->findOrFail($id);

        // Apakah sudah ada di cart user?
        $cartItem = null;
        if ($request->user()) {
            $cartItem = Cart::where('user_id', $request->user()->id)
                ->where('surplus_id', $surplus->id)
                ->first();
        }

        // Surplus lain dari toko yang sama
        $relatedSurplus = SurplusProduct::with(['product.productImg'])
            ->whereHas('product', fn($q) => $q->where(
                'store_id',
                $surplus->product->store_id
            ))
            ->where('id', '!=', $surplus->id)
            ->where('remaining_quantity', '>', 0)
            ->whereDate('expired_at', '>=', now())
            ->take(4)
            ->get();

        return view('user.surplus.show', compact('surplus', 'cartItem', 'relatedSurplus'));
    }
}
