<?php

namespace App\Services\SurplusProduct;

use App\Models\SurplusProduct;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SurplusProductService
{

    public function getNearby(float $lat, float $lng, float $radius = 5)
    {
        $distanceFormula = "(
        6371 * acos(
            LEAST(
                1.0,
                cos(radians(?)) * cos(radians(stores.latitude))
                * cos(radians(stores.longitude) - radians(?))
                + sin(radians(?)) * sin(radians(stores.latitude))
            )
        )
    )";

        return SurplusProduct::query()
            ->with([
                'product.store:id,name,address,latitude,longitude',
                'product.category:id,name',
                'product.productImg',
            ])
            ->join('products', 'surplus_products.product_id', '=', 'products.id')
            ->join('stores', 'products.store_id', '=', 'stores.id')
            ->where('stores.is_active', true)
            ->where('products.is_active', true)
            ->where('surplus_products.status', 'active')
            ->where('surplus_products.remaining_quantity', '>', 0)
            ->where(function ($q) {
                $q->whereNull('surplus_products.expired_at')
                    ->orWhere('surplus_products.expired_at', '>', now());
            })
            ->selectRaw("
            surplus_products.*,
            {$distanceFormula} AS distance_km
        ", [$lat, $lng, $lat])

            // PostgreSQL aman pakai whereRaw ulang
            ->whereRaw("
            {$distanceFormula} <= ?
        ", [$lat, $lng, $lat, $radius])

            ->orderBy('distance_km')
            ->paginate(10);
    }

    public function createSurplusProduct(array $data)
    {
        return DB::transaction(function () use ($data) {

            return SurplusProduct::create([
                'product_id' => $data['product_id'],
                'initial_price' => $data['initial_price'],
                'discount_price' => $data['discount_price'],
                'quantity' => $data['quantity'],
                'remaining_quantity' => $data['quantity'],
                'expired_at' => $data['expired_at'],
                'pickup_start_at' => $data['pickup_start_at'],
                'pickup_end_at' => $data['pickup_end_at'],
                'status' => 'active',
            ]);
        });
    }

    public function updateSurplusProduct(string $id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {

            $surplus = SurplusProduct::findOrFail($id);

            // Tidak boleh update jika expired
            if ($this->isExpired($surplus)) {
                throw ValidationException::withMessages([
                    'surplus' => 'Produk sudah kadaluarsa dan tidak bisa diubah.',
                ]);
            }


            // Tidak boleh update jika sold out
            if ($surplus->status === 'sold_out') {
                throw ValidationException::withMessages([
                    'surplus' => 'Produk sudah habis dan tidak bisa diubah.',
                ]);
            }

            // Cegah manipulasi quantity
            if (
                isset($data['quantity']) &&
                $data['quantity'] != $surplus->quantity
            ) {
                throw ValidationException::withMessages([
                    'quantity' => 'Quantity tidak boleh diubah setelah dibuat.',
                ]);
            }

            // Update field yang boleh
            $surplus->update([
                'initial_price' => $data['initial_price'] ?? $surplus->initial_price,
                'discount_price' => $data['discount_price'] ?? $surplus->discount_price,
                'expired_at' => $data['expired_at'] ?? $surplus->expired_at,
                'pickup_start_at' => $data['pickup_start_at'] ?? $surplus->pickup_start_at,
                'pickup_end_at' => $data['pickup_end_at'] ?? $surplus->pickup_end_at,
            ]);

            // Sync status setelah update
            $this->syncStatus($surplus);

            return $surplus;
        });
    }

    public function deleteSurplusProduct(string $id): void
    {
        DB::transaction(function () use ($id) {

            $surplus = SurplusProduct::findOrFail($id);

            // Tidak boleh delete jika sold out
            if ($surplus->status === 'sold_out') {
                throw ValidationException::withMessages([
                    'surplus' => 'Produk sudah habis dan tidak bisa dihapus.',
                ]);
            }

            // Tidak boleh delete jika expired
            if ($this->isExpired($surplus)) {
                throw ValidationException::withMessages([
                    'surplus' => 'Produk sudah kadaluarsa dan tidak bisa dihapus.',
                ]);
            }

            $surplus->delete();
        });
    }

    public function reduceStock(string $id, int $qty): SurplusProduct
    {
        return DB::transaction(function () use ($id, $qty) {

            $surplus = SurplusProduct::lockForUpdate()->findOrFail($id);

            if ($this->isExpired($surplus)) {
                throw ValidationException::withMessages([
                    'surplus' => 'Produk sudah kadaluarsa.',
                ]);
            }

            if ($surplus->remaining_quantity < $qty) {
                throw ValidationException::withMessages([
                    'surplus' => 'Stok tidak mencukupi.',
                ]);
            }

            $surplus->remaining_quantity -= $qty;

            // Update status jika habis
            if ($surplus->remaining_quantity === 0) {
                $surplus->status = 'sold_out';
            }

            $surplus->save();

            broadcast(new \App\Events\SurplusStatusUpdated($surplus));

            return $surplus;
        });
    }

    private function syncStatus(SurplusProduct $surplus): void
    {
        if ($this->isExpired($surplus)) {
            $surplus->status = 'expired';
        } elseif ($surplus->remaining_quantity === 0) {
            $surplus->status = 'sold_out';
        } else {
            $surplus->status = 'active';
        }

        $surplus->save();
    }

    private function isExpired(SurplusProduct $surplus): bool
    {
        return Carbon::parse($surplus->expired_at)->isPast();
    }
}
