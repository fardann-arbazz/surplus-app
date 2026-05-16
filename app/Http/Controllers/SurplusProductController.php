<?php

namespace App\Http\Controllers;

use App\Http\Requests\SurplusProduct\CreateSurplusProductRequest;
use App\Http\Requests\SurplusProduct\UpdateSurplusProductRequest;
use App\Models\CategoryProducts;
use App\Models\SurplusProduct;
use App\Services\SurplusProduct\SurplusProductService;
use Illuminate\Http\Request;

class SurplusProductController extends Controller
{
    public function __construct(private SurplusProductService $surplusProductService) {}

    public function index()
    {
        $categories = CategoryProducts::all();

        return view('user.surplus-menu', compact('categories'));
    }

    public function getNearby(Request $request)
    {
        $user = $request->user();

        // Belum pernah set lokasi
        if (is_null($user->latitude) || is_null($user->longitude)) {
            return response()->json([
                'message' => 'Lokasi belum diset. Silakan pilih lokasi terlebih dahulu.',
                'code'    => 'LOCATION_NOT_SET',
            ], 403);
        }

        $radius = (float) $request->query('radius', 5);
        $radius = min(max($radius, 1), 20);

        $products = $this->surplusProductService->getNearby(
            lat: (float) $user->latitude,
            lng: (float) $user->longitude,
            radius: $radius,
        );

        return response()->json($products);
    }

    public function create(CreateSurplusProductRequest $request)
    {
        $this->surplusProductService->createSurplusProduct($request->validated());

        return redirect()->back()->with('success', 'Surplus product berhasil dilakukan!');
    }

    public function update(string $id, UpdateSurplusProductRequest $request)
    {
        $this->surplusProductService->updateSurplusProduct($id, $request->validated());

        return redirect()->back()->with('success', 'Update surplus product berhasil dilakukan!');
    }

    public function delete(string $id)
    {
        $this->surplusProductService->deleteSurplusProduct($id);

        return redirect()->back()->with('success', 'Delete surplus product berhasil dilakukan!');
    }
}
