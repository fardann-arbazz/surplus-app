<?php

namespace App\Services\Category;

use App\Models\CategoryProducts;

class CategoryService
{
    public function createCategory(array $data)
    {
        $category = CategoryProducts::create([
            'name' => $data['name']
        ]);

        return $category;
    }

    public function updateCategory(string $id, array $data)
    {
        $category = CategoryProducts::findOrFail($id);
        $category->name = $data['name'];
        $category->save();

        return $category;
    }

    public function deleteCategory(string $id)
    {
        $category = CategoryProducts::findOrFail($id);
        $category->delete();

        return;
    }
}
