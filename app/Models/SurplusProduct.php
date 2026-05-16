<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

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

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItems::class, 'surplus_id');
    }

    public function cart(): HasOne
    {
        return $this->hasOne(Cart::class, 'surplus_id');
    }

    protected $casts = [
        'expired_at' => 'datetime',
    ];
}
