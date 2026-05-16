<?php

namespace App\Http\Controllers\Seller;

use App\Exceptions\Order\InvalidStatusTransitionException;
use App\Exceptions\Order\OrderNotFoundException;
use App\Http\Controllers\Controller;
use App\Services\Orders\OrderService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(private readonly OrderService $orderService) {}

    /* ──────────────────────────────────────────────────────────
     * GET /seller/orders?status=paid
     * Daftar order masuk ke toko seller (dengan filter status)
     * ────────────────────────────────────────────────────────── */
    public function index(Request $request)
    {
        $status = $request->query('status');

        $orders = $this->orderService->getSellerOrders(
            seller: $request->user(),
            status: $status,
        );

        return view('seller.orders.index', compact('orders', 'status'));
    }

    /* ──────────────────────────────────────────────────────────
     * GET /seller/orders/{orderId}
     * Detail order
     * ────────────────────────────────────────────────────────── */
    public function show(Request $request, int $orderId)
    {
        try {
            $order = $this->orderService->getOrderDetail($orderId, $request->user());
            return view('seller.orders.show', compact('order'));
        } catch (OrderNotFoundException) {
            abort(404, 'Order tidak ditemukan.');
        }
    }

    /* ──────────────────────────────────────────────────────────
     * PATCH /seller/orders/{orderId}/ready
     * Seller konfirmasi pesanan siap diambil.
     * Order harus dalam status 'paid'.
     * Setelah ini, pickup_code ditampilkan ke seller.
     * ────────────────────────────────────────────────────────── */
    public function markReady(Request $request, int $orderId)
    {
        try {
            $order   = $this->orderService->getOrderDetail($orderId, $request->user());
            $updated = $this->orderService->markReadyForPickup($order, $request->user());

            return redirect()->route('seller.orders.show', $orderId)
                ->with('success', 'Pesanan ditandai siap diambil.')
                ->with('pickup_code', $updated->pickup_code);
            // ↑ flash pickup_code ke session agar bisa ditampilkan
            //   secara prominent di halaman detail (gunakan session('pickup_code'))

        } catch (OrderNotFoundException) {
            abort(404, 'Order tidak ditemukan.');
        } catch (InvalidStatusTransitionException $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
