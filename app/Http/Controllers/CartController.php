<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CartController extends Controller
{
    // 取得購物車
    public function index()
    {
        $cart = $this->getOrCreateCart();
        $cart->load('items.product');

        return Inertia::render('Cart/Index', [
            'cart' => $cart,
            'total' => $cart->total,
        ]);
    }

    // 加入購物車
    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'required|integer|min:1',
        ]);

        $product = Product::findOrFail($request->product_id);

        if ($product->stock < $request->quantity) {
            return back()->withErrors(['quantity' => '庫存不足']);
        }

        $cart = $this->getOrCreateCart();

        $item = $cart->items()->where('product_id', $product->id)->first();

        if ($item) {
            $item->increment('quantity', $request->quantity);
        } else {
            $cart->items()->create([
                'product_id' => $product->id,
                'quantity'   => $request->quantity,
                'price' => $product->discount ? $product->price * $product->discount : $product->price,
            ]);
        }

        return back()->with('success', '已加入購物車');
    }

    // 更新數量
    public function update(Request $request, CartItem $item)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $item->update(['quantity' => $request->quantity]);

        return back()->with('success', '已更新數量');
    }

    // 刪除項目
    public function remove(CartItem $item)
    {
        $item->delete();

        return back()->with('success', '已移除商品');
    }

    // 取得或建立使用者的 active 購物車
    private function getOrCreateCart(): Cart
    {
        return Cart::firstOrCreate(
            ['user_id' => auth()->id(), 'status' => 'active']
        );
    }
}