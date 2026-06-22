<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProductController extends Controller
{
    public function index(Request $request): Response
    {
        $user = auth()->user();
        $adminId = $user->managed_by ?? ($user->hasRole('admin') ? $user->id : null);

        $query = Product::with(['category.parent'])
            ->where('is_active', true);

        // 根據 admin 過濾商品
        if ($adminId) {
            $query->where('admin_id', $adminId);
        }

        // 分類篩選
        if ($request->filled('category')) {
            $query->whereHas('category', fn ($q) => $q->where('slug', $request->category));
        }

        // 搜尋
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $products = $query->latest()->paginate(12);

        // 只取該 admin 的分類
        $categories = ProductCategory::whereNotNull('parent_id')
            ->when($adminId, fn ($q) => $q->where('admin_id', $adminId))
            ->with('parent')
            ->get();

        return Inertia::render('Products/Index', [
            'products'   => $products,
            'categories' => $categories,
            'filters'    => $request->only(['category', 'search']),
        ]);
    }

    public function indexApi(Request $request): \Illuminate\Http\JsonResponse
    {
        $user = auth()->user();
        $adminId = $user->managed_by ?? ($user->hasRole('admin') ? $user->id : null);

        $query = Product::with(['category.parent'])
            ->where('is_active', true);

        if ($adminId) {
            $query->where('admin_id', $adminId);
        }

        if ($request->filled('category')) {
            $query->whereHas('category', fn ($q) => $q->where('slug', $request->category));
        }

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $products = $query->latest()->paginate(12);

        $categories = ProductCategory::whereNotNull('parent_id')
            ->when($adminId, fn ($q) => $q->where('admin_id', $adminId))
            ->with('parent')
            ->get();

        return response()->json([
            'products'   => $products,
            'categories' => $categories,
        ]);
    }

    public function show(string $uuid): Response
    {
        $user = auth()->user();
        $adminId = $user->managed_by ?? ($user->hasRole('admin') ? $user->id : null);

        $query = Product::with(['category.parent'])
            ->where('uuid', $uuid)
            ->where('is_active', true);

        if ($adminId) {
            $query->where('admin_id', $adminId);
        }

        $product = $query->firstOrFail();

        return Inertia::render('Products/Show', [
            'product' => $product,
        ]);
    }
}