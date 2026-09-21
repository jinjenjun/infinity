<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProductController extends Controller
{
    public function index(Request $request): Response
    {
        $viewer = auth()->user();

        $query = Product::with(['category.parent'])
            ->where('is_active', true)
            ->visibleTo($viewer);

        // 分類篩選
        if ($request->filled('category')) {
            $query->whereHas('category', fn ($q) => $q->where('slug', $request->category));
        }

        // 搜尋
        if ($request->filled('search')) {
            $query->where('name', 'like', '%'.$request->search.'%');
        }

        $products = $query->oldest()->paginate(12);

        $categories = ProductCategory::whereNotNull('parent_id')
            ->visibleTo($viewer)
            ->with('parent')
            ->get();

        return Inertia::render('Products/Index', [
            'products' => $products,
            'categories' => $categories,
            'filters' => $request->only(['category', 'search']),
        ]);
    }

    public function indexApi(Request $request): JsonResponse
    {
        $viewer = auth()->user();

        $query = Product::with(['category.parent'])
            ->where('is_active', true)
            ->visibleTo($viewer);

        if ($request->filled('category')) {
            $query->whereHas('category', fn ($q) => $q->where('slug', $request->category));
        }

        if ($request->filled('search')) {
            $query->where('name', 'like', '%'.$request->search.'%');
        }

        $products = $query->oldest()->paginate(12);

        $categories = ProductCategory::whereNotNull('parent_id')
            ->visibleTo($viewer)
            ->with('parent')
            ->get();

        return response()->json([
            'products' => $products,
            'categories' => $categories,
        ]);
    }

    public function show(string $uuid): Response
    {
        $viewer = auth()->user();

        $product = Product::with(['category.parent'])
            ->where('uuid', $uuid)
            ->where('is_active', true)
            ->visibleTo($viewer)
            ->firstOrFail();

        return Inertia::render('Products/Show', [
            'product' => $product,
        ]);
    }
}
