<?php

namespace App\Http\Controllers;

use App\Http\Requests\Stores\CreateStoresRequest;
use App\Models\Stores;
use App\Services\Seller\NearbyStoresService;
use App\Services\Seller\SellerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Throwable;

class StoresController extends Controller
{
    public function __construct(private SellerService $storesService, private NearbyStoresService $nearbyStoreService) {}

    public function index(): View
    {
        return view('seller.create-seller');
    }

    public function getNearbyStores(Request $request)
    {
        $lat = session('user_latitude');
        $lng = session('user_longitude');

        if (!$lat || !$lng) {
            return response()->json([
                'message' => 'Lokasi belum diset',
                'code' => 'LOCATION_NOT_SET',
            ], 403);
        }

        $radius = (float) $request->radius ?? 5;

        $stores = $this->nearbyStoreService->getNearby(
            lat: (float) $lat,
            lng: (float) $lng,
            radius: $radius
        );

        return response()->json($stores);
    }

   
    public function createStore(CreateStoresRequest $request)
    {
        try {
            $user = Auth::user();
            $this->storesService->createSeller($user->id, $request->validated(), $request->file('img_url'));

            return response()->json(['message' => 'Store berhasil dibuat'], 200);
        } catch (Throwable $th) {
            report($th);

            return response()->json(['message' => 'Terjadi kesalahan saat membuat store.'], 500);
        }
    }

    // public 
}
