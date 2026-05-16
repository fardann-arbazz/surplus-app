<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Stores extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'description',
        'address',
        'img_url',
        'latitude',
        'longitude',
        'is_active',
        'is_online'
    ];

    protected $appends = ['image_url', 'formatted_date'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function product(): HasMany
    {
        return $this->hasMany(Products::class, 'store_id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Orders::class, 'store_id');
    }

    // accessor modern 
    protected function imageUrl(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->img_url
                ? asset('storage/' . $this->img_url)
                : asset('images/default-store.png') // fallback
        );
    }

    protected function formattedDate(): Attribute
    {
        return Attribute::make(
            get: fn() => Carbon::parse($this->created_at)->translatedFormat('d M Y')
        );
    }
}
