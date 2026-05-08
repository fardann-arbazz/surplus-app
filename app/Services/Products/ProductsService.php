<?php

namespace App\Services\Products;

use App\Models\Products;
use App\Models\ProductsImg;
use App\Models\Stores;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductsService
{
    public function createProducts(string $storeId, array $data, array $images)
    {
        return DB::transaction(function () use ($storeId, $data, $images) {
            $store = Stores::findOrFail($storeId);

            if (count($images) > 8) {
                throw new \Exception('Max 8 images allowed');
            }

            $product = Products::create([
                'store_id' => $store->id,
                'name'        => $data['name'],
                'description' => $data['description'] ?? null,
                'price'       => $data['price'],
                'category_id' => $data['category_id'],
                'is_active'   => $data['is_active'] ?? true,
            ]);

            $this->storeImages($store, $product, $images);

            return $product;
        });
    }

    public function updateProducts(string $storeId, string $productId, array $data, ?array $images)
    {
        return DB::transaction(function () use ($storeId, $productId, $data, $images) {
            $store = Stores::findOrFail($storeId);
            $product = Products::where('id', $productId)->where('store_id', $storeId)->firstOrFail();

            // update field produk
            $product->update([
                'name'        => $data['name'],
                'description' => $data['description'] ?? null,
                'price'       => $data['price'],
                'category_id' => $data['category_id'],
                'is_active'   => $data['is_active'] ?? $product->is_active,
            ]);

            // jika ada images baru, hapus yang lama lalu simpan yang baru
            if (!empty($images)) {
                if (count($images) > 8) {
                    throw new \Exception('Max 8 images allowed');
                }

                $this->deleteImages($product);
                $this->storeImages($store, $product, $images);
            }

            return $product;
        });
    }

    public function deleteProduct(string $productId): void
    {
        $product = Products::where('id', $productId)->firstOrFail();
        if (!$product) {
            throw new \Exception('Product tidak ditemukan');
        }

        $this->deleteImages($product);

        $product->delete();
    }

    public function deleteImages(Products $product): void
    {
        $oldImages = ProductsImg::where('product_id', $product->id)->get();

        foreach ($oldImages as $img) {
            // hapus file fisik dari storage/public
            if (Storage::disk('public')->exists($img->img_url)) {
                Storage::disk('public')->delete($img->img_url);
            }
        }

        // hapus semua record dari DB sekaligus
        ProductsImg::where('product_id', $product->id)->delete();
    }

    public function storeImages(Stores $store,  Products $product, array $images)
    {
        // sanitize nama toko 
        $storeName = Str::slug($store->name);

        foreach ($images as $index => $image) {
            // generate nama file unik
            $fileName = time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();

            // path: /{nama_toko}/foods/{filename}
            $path = $image->storeAs(
                "{$storeName}/foods",
                $fileName,
                'public'
            );

            // simpan ke database
            ProductsImg::create([
                'product_id' => $product->id,
                'img_url'  => $path,
                'is_primary' => $index === 0
            ]);
        }
    }
}
