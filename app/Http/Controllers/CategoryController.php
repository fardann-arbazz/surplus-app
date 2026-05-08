<?php

namespace App\Http\Controllers;

use App\Http\Requests\Category\CreateCategoryRequest;
use App\Models\CategoryProducts;
use App\Services\Category\CategoryService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function __construct(
        private CategoryService $categoryService
    ) {}

    public function index(): View
    {
        $category = CategoryProducts::paginate(10);

        return view('admin.category-management', [
            'category' => $category
        ]);
    }

    public function createCategory(CreateCategoryRequest $request)
    {
        $this->categoryService->createCategory($request->validated());

        return redirect()->route('admin.category-management')->with('success', 'Create category success');
    }
}
