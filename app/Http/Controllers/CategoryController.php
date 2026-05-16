<?php

namespace App\Http\Controllers;

use App\Http\Requests\Category\CreateCategoryRequest;
use App\Http\Requests\Category\UpdateCategoryRequest;
use App\Models\CategoryProducts;
use App\Services\Category\CategoryService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function __construct(
        private CategoryService $categoryService
    ) {}

    public function index(Request $request): View
    {
        $search = $request->search;

        $category = CategoryProducts::withCount('product')
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.category-management', compact('category', 'search'));
    }


    public function store(CreateCategoryRequest $request)
    {
        $this->categoryService->createCategory($request->validated());

        return redirect()->route('admin.category.index')->with('success', 'Create category success');
    }

    public function update(string $id, UpdateCategoryRequest $request)
    {
        $this->categoryService->updateCategory($id, $request->validated());

        return redirect()->route('admin.category.index')->with('success', 'Create category success');
    }

    public function destroy(string $id)
    {
        $this->categoryService->deleteCategory($id);

        return redirect()->route('admin.category.index')->with('success', 'Create category success');
    }
}
