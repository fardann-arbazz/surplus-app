<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Orders extends Model
{
    protected $fillable = [
        'user_id',
        'store_id',
        'total_price',
        'status',
        'payment_reference',
        'snap_token',
        'expires_at',
        'paid_at',
        'pickup_code',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'paid_at' => 'datetime',
        'status' => OrderStatus::class,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItems::class, 'order_id');
    }

    public function stores(): BelongsTo
    {
        return $this->belongsTo(Stores::class, 'store_id');
    }

    public function isPending(): bool
    {
        return $this->status === OrderStatus::Pending;
    }

    public function isPaid(): bool
    {
        return $this->status === OrderStatus::Paid;
    }

    public function isExpired(): bool
    {
        return $this->status === OrderStatus::Expired;
    }

    /** Apakah sudah melewati deadline bayar */
    public function isPaymentExpired(): bool
    {
        return $this->expires_at && now()->isAfter($this->expires_at);
    }

    public function scopeForStore($query, int $storeId)
    {
        return $query->where('store_id', $storeId);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopePaid($query)
    {
        return $query->whereIn('status', ['paid', 'ready_for_pickup', 'completed']);
    }
}
