<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Midtrans\MidtransService;
use App\Services\Orders\OrderService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Webhook dari Midtrans — tetap API (tidak pakai Blade).
 * Diakses langsung oleh server Midtrans, bukan browser user.
 *
 * WAJIB exclude dari CSRF:
 *   app/Http/Middleware/VerifyCsrfToken.php → $except[]
 *   atau di bootstrap/app.php (Laravel 11):
 *   ->withMiddleware(fn($m) => $m->validateCsrfTokens(except: ['api/midtrans/webhook']))
 */
class MidtransWebhookController extends Controller
{
    public function __construct(
        private readonly OrderService    $orderService,
        private readonly MidtransService $midtrans,
    ) {}

    /* ──────────────────────────────────────────────────────────
     * POST /api/midtrans/webhook
     *
     * Endpoint ini TIDAK pakai auth middleware.
     * Keamanan dijaga lewat verifikasi signature_key Midtrans.
     * ────────────────────────────────────────────────────────── */
    public function handle(Request $request): Response
    {
        \Log::info('MIDTRANS WEBHOOK MASUK', $request->all());
        $payload = $request->all();

        // ── Verifikasi signature Midtrans ──────────────────────
        if (! $this->midtrans->verifySignature($payload)) {
            return response('Unauthorized', 401);
        }

        // ── Proses notifikasi ──────────────────────────────────
        try {
            $this->orderService->handlePaymentNotification($payload);
        } catch (\Exception $e) {
            report($e);
            // Kembalikan 200 supaya Midtrans tidak retry terus-menerus
            return response('Error logged', 200);
        }

        return response('OK', 200);
    }
}
