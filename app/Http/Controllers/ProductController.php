<?php

namespace App\Http\Controllers;

use App\Http\Requests\Products\CreateProducts;
use App\Http\Requests\Products\UpdateProducts;
use App\Models\CategoryProducts;
use App\Models\Products;
use App\Models\SurplusProduct;
use App\Services\Products\ProductsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    public function __construct(private ProductsService $productService) {}

    public function index()
    {
        $user = Auth::user()->load('stores');
        $store = $user->stores->first();
        $regularMenus = Products::where('store_id', $store->id)->with('category', 'productImg')->paginate(10);
        $surplusMenus =  SurplusProduct::with(['product.category', 'product.productImg'])
            ->whereHas('product', function ($query) use ($store) {
                $query->where('store_id', $store->id);
            })
            ->paginate(10);

        $category = CategoryProducts::all();

        return view('seller.menu', compact('regularMenus', 'surplusMenus', 'category'));
    }

    public function createProduct(CreateProducts $request)
    {
        $user = Auth::user()->load('stores');
        $store = $user->stores->first();

        $request->merge(['store_id' => $store->id]);

        $this->productService->createProducts(
            $store->id,
            $request->validated(),
            $request->file('images')
        );

        return redirect()->back()->with('success', 'Product berhasil dibuat!');
    }

    public function updateProduct(UpdateProducts $request, string $productId)
    {
        $user = Auth::user()->load('stores');
        $store = $user->stores->first();

        $request->merge(['store_id' => $store->id]);

        $this->productService->updateProducts(
            $store->id,
            $productId,
            $request->validated(),
            $request->hasFile('images') ? $request->file('images') : null
        );

        return redirect()->back()->with('success', 'Update product berhasil dibuat!');
    }

    public function deleteProduct(string $productId)
    {
        $this->productService->deleteProduct($productId);

        return redirect()->back()->with('success', 'Delete product success!');
    }
}
