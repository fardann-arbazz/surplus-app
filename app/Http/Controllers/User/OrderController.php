<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Exceptions\Order\InsufficientStockException;
use App\Exceptions\Order\InvalidStatusTransitionException;
use App\Exceptions\Order\MultiStoreOrderException;
use App\Exceptions\Order\OrderNotFoundException;
use App\Exceptions\Order\SurplusExpiredException;
use App\Http\Requests\Order\CheckoutRequest;
use App\Http\Requests\Order\ConfirmPickupRequest;
use App\Services\Midtrans\MidtransService;
use App\Services\Orders\OrderService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(private readonly OrderService $orderService, private readonly MidtransService $midtransService) {}

    /* ──────────────────────────────────────────────────────────
     * GET /user/orders
     * Tampilkan daftar order milik user (dengan filter status)
     * ────────────────────────────────────────────────────────── */
    public function index(Request $request)
    {
        $status = $request->query('status');

        $orders = $this->orderService->getUserOrders(
            user: $request->user(),
            status: $status,
        );

        return view('user.orders.index', compact('orders', 'status'));
    }

    /* ──────────────────────────────────────────────────────────
     * GET /user/orders/{orderId}
     * Detail order
     * ────────────────────────────────────────────────────────── */
    public function show(Request $request, int $orderId)
    {
        try {
            $order = $this->orderService->getOrderDetail($orderId, $request->user());

            $snapToken = null;

            if ($order->isPending() && ! $order->isPaymentExpired()) {
                // Pakai token yang sudah ada
                $snapToken = $order->snap_token;
            }

            return view('user.orders.show', compact('order', 'snapToken'));
        } catch (OrderNotFoundException) {
            abort(404, 'Order tidak ditemukan.');
        }
    }

    /* ──────────────────────────────────────────────────────────
     * GET /user/checkout
     * Tampilkan halaman checkout (form review keranjang)
     * ────────────────────────────────────────────────────────── */
    public function checkoutPage(Request $request)
    {
        // Ambil cart dari session yang sudah diisi sebelumnya
        $cartItems = session('cart', []);

        if (empty($cartItems)) {
            return redirect()->route('user.cart.index')
                ->with('error', 'Keranjang belanja kosong.');
        }

        return view('user.orders.checkout', compact('cartItems'));
    }

    /* ──────────────────────────────────────────────────────────
     * POST /user/orders/checkout
     * Proses checkout → buat order + Midtrans Snap token
     * Redirect ke halaman pembayaran dengan snap_token
     * ────────────────────────────────────────────────────────── */
    public function checkout(CheckoutRequest $request)
    {
        try {
            $result = $this->orderService->checkout(
                user: $request->user(),
                items: $request->validated('items'),
            );

            // Kosongkan cart dari session setelah order berhasil dibuat
            session()->forget('cart');

            // Simpan snap_token ke session agar bisa dipakai di halaman payment
            session(['snap_token' => $result['snap_token']]);

            return redirect()->route('user.orders.payment', $result['order']->id)
                ->with('success', 'Order berhasil dibuat. Silahkan selesaikan pembayaran.');
        } catch (MultiStoreOrderException $e) {
            return back()->withErrors(['items' => $e->getMessage()]);
        } catch (InsufficientStockException | SurplusExpiredException $e) {
            return back()->withErrors(['items' => $e->getMessage()]);
        } catch (\Exception $e) {
            report($e);
            return back()->with('error', 'Terjadi kesalahan, coba lagi.');
        }
    }

    /* ──────────────────────────────────────────────────────────
     * GET /user/orders/{orderId}/payment
     * Halaman pembayaran Midtrans Snap
     * snap_token diambil dari session (hanya tersedia sekali)
     * ────────────────────────────────────────────────────────── */
    public function paymentPage(Request $request, int $orderId)
    {
        try {
            $order     = $this->orderService->getOrderDetail($orderId, $request->user());
            $snapToken = $order->snap_token; // ambil dari database

            if (! $snapToken || ! $order->isPending()) {
                return redirect()->route('user.orders.show', $orderId)
                    ->with('info', 'Pembayaran sudah tidak tersedia untuk order ini.');
            }

            return view('user.orders.payment', compact('order', 'snapToken'));
        } catch (OrderNotFoundException) {
            abort(404, 'Order tidak ditemukan.');
        }
    }

    /* ──────────────────────────────────────────────────────────
     * GET /user/orders/{orderId}/confirm-pickup
     * Tampilkan form input kode pickup
     * ────────────────────────────────────────────────────────── */
    public function confirmPickupPage(Request $request, int $orderId)
    {
        try {
            $order = $this->orderService->getOrderDetail($orderId, $request->user());

            if ($order->status->value !== 'ready_for_pickup') {
                return redirect()->route('user.orders.show', $orderId)
                    ->with('error', 'Order belum siap diambil.');
            }

            return view('user.orders.confirm-pickup', compact('order'));
        } catch (OrderNotFoundException) {
            abort(404, 'Order tidak ditemukan.');
        }
    }

    /* ──────────────────────────────────────────────────────────
     * POST /user/orders/{orderId}/confirm-pickup
     * Proses konfirmasi pickup dengan kode dari seller
     * ────────────────────────────────────────────────────────── */
    public function confirmPickup(ConfirmPickupRequest $request, int $orderId)
    {
        try {
            $order = $this->orderService->getOrderDetail($orderId, $request->user());

            $this->orderService->confirmPickup(
                order: $order,
                user: $request->user(),
                pickupCode: $request->validated('pickup_code'),
            );

            return redirect()->route('user.orders.show', $orderId)
                ->with('success', 'Pickup dikonfirmasi. Terima kasih sudah berbelanja!');
        } catch (OrderNotFoundException) {
            abort(404, 'Order tidak ditemukan.');
        } catch (InvalidStatusTransitionException $e) {
            return back()->with('error', $e->getMessage());
        } catch (\InvalidArgumentException $e) {
            return back()->withErrors(['pickup_code' => $e->getMessage()]);
        }
    }
}
