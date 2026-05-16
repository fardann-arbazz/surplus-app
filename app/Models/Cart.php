<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cart extends Model
{
    protected $fillable = [
        'user_id',
        'surplus_id',
        'quantity',
    ];

    protected $casts = [
        'quantity' => 'integer',
    ];


    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function surplus(): BelongsTo
    {
        return $this->belongsTo(SurplusProduct::class, 'surplus_id');
    }

    /* ── Helpers ────────────────────────────────────────────── */

    public function subtotal(): float
    {
        $price = $this->surplus->discount_price
            ?? $this->surplus->product->price
            ?? 0;

        return $price * $this->quantity;
    }
}
