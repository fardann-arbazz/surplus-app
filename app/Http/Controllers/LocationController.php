<?php

namespace App\Http\Controllers;

use App\Http\Requests\Location\UpdateLocationRequest;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function update(UpdateLocationRequest $request)
    {
        $user = $request->user();

        $user->update([
            'latitude'            => $request->latitude,
            'longitude'           => $request->longitude,
            'location_updated_at' => now(),
        ]);

        session([
            'user_latitude' => $request->latitude,
            'user_longitude' => $request->longitude,
        ]);

        return response()->json([
            'message' => 'Lokasi berhasil disimpan.',
            'data'    => [
                'latitude'   => (float) $user->latitude,
                'longitude'  => (float) $user->longitude,
                'updated_at' => $user->location_updated_at->toIso8601String(),
            ],
        ]);
    }

    public function status(Request $request)
    {
        $user = $request->user();

        $hasLocation = ! is_null($user->latitude) && ! is_null($user->longitude);

        return response()->json([
            'has_location' => $hasLocation,
            'latitude'     => $hasLocation ? (float) $user->latitude  : null,
            'longitude'    => $hasLocation ? (float) $user->longitude : null,
            'updated_at'   => $user->location_updated_at?->toIso8601String(),
        ]);
    }
}
