<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Products extends Model
{
    protected $fillable = [
        'store_id',
        'name',
        'description',
        'price',
        'category_id',
        'is_active'
    ];

    public function store(): BelongsTo
    {
        return $this->belongsTo(Stores::class, 'store_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(CategoryProducts::class);
    }

    public function productImg(): HasMany
    {
        return $this->hasMany(ProductsImg::class, 'product_id');
    }
}
