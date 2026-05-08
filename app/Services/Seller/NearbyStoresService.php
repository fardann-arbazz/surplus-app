<?php

namespace App\Services\Seller;

use App\Models\Stores;

class NearbyStoresService
{
    public function getNearby(
        float $lat,
        float $lng,
        float $radius = 5,
        int $perPage = 10
    ) {
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

        return Stores::query()
            ->with([
                'product' => function ($query) {
                    $query
                        ->where('is_active', true)
                        ->with([
                            'category:id,name',
                            'productImg',
                        ]);
                }
            ])

            ->where('stores.is_active', true)

            // valid coordinate
            ->whereNotNull('stores.latitude')
            ->whereNotNull('stores.longitude')

            ->selectRaw("
                stores.*,
                {$distanceFormula} AS distance_km
            ", [$lat, $lng, $lat])

            ->whereRaw("
                {$distanceFormula} <= ?
            ", [$lat, $lng, $lat, $radius])

            ->orderBy('distance_km')
            ->paginate($perPage);
    }
}
