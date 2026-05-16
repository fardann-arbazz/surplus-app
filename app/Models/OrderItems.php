<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItems extends Model
{
    protected $fillable = [
        'order_id',
        'surplus_id',
        'quantity',
        'price',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Orders::class, 'order_id');
    }

    public function surplusProduct(): BelongsTo
    {
        return $this->belongsTo(SurplusProduct::class, 'surplus_id');
    }

    public function subtotal(): int
    {
        return $this->price * $this->quantity;
    }
}
