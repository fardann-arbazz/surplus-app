<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductsImg extends Model
{
    protected $fillable = [
        'product_id',
        'img_url',
        'is_primary'
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Products::class, 'product_id');
    }

    protected function imgUrl(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $value
                ? asset('storage/' . $value)
                : asset('images/default-product.png')
        );
    }

    public function surplusProduct()
    {
        return $this->hasMany(SurplusProduct::class, 'product_id');
    }
}
