<?php

namespace App\Services\Midtrans;

use App\Models\Orders;
use App\Models\User;

class MidtransService
{
    public function __construct()
    {
        \Midtrans\Config::$serverKey        = config('midtrans.server_key');
        \Midtrans\Config::$isProduction     = config('midtrans.is_production');
        \Midtrans\Config::$isSanitized      = true;
        \Midtrans\Config::$is3ds            = true;
    }

    /**
     * Buat Snap token untuk order baru.
     * Return: snap_token (string) — dikirim ke frontend untuk trigger Snap popup.
     */
    public function createSnapToken(Orders $order, User $user): string
    {
        $params = [
            'transaction_details' => [
                'order_id'     => (string) 'ORDER-' . $order->id,
                'gross_amount' => (int)    $order->total_price,
            ],
            'customer_details' => [
                'first_name' => $user->name,
                'email'      => $user->email,
                'phone'      => $user->phone ?? '',
            ],
            'callbacks' => [
                'finish' => route('user.orders.show', $order->id),
            ],
            'item_details' => $order->orderItems->map(function ($item) {
                return [
                    'id'       => (string) $item->surplus_id,
                    'price'    => (int)    $item->price,
                    'quantity' => (int)    $item->quantity,
                    'name'     => $item->surplus->product->name ?? "Item #{$item->surplus_id}",
                ];
            })->toArray(),
            'expiry' => [
                'unit'     => 'minutes',
                'duration' => 15,
            ],
        ];

        return \Midtrans\Snap::getSnapToken($params);
    }

    /**
     * Verifikasi signature dari webhook Midtrans.
     *
     * signature_key = SHA512(order_id + status_code + gross_amount + server_key)
     */
    public function verifySignature(array $payload): bool
    {
        $expected = hash(
            'sha512',
            $payload['order_id']
                . $payload['status_code']
                . $payload['gross_amount']
                . config('midtrans.server_key')
        );

        return hash_equals($expected, $payload['signature_key'] ?? '');
    }
}
