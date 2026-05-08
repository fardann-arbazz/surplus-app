<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SurplusProduct extends Model
{
    protected $fillable = [
        'product_id',
        'initial_price',
        'discount_price',
        'quantity',
        'remaining_quantity',
        'expired_at',
        'pickup_start_at',
        'pickup_end_at',
        'status'
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Products::class, 'product_id');
    }
}
