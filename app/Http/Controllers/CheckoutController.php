<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CheckoutController extends Controller
{
    // 結帳頁面
    public function index()
    {
        $cart = Cart::where('user_id', auth()->id())
            ->where('status', 'active')
            ->with('items.product')
            ->first();

        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', '購物車是空的');
        }

        $total = $cart->items->sum(fn($item) => $item->price * $item->quantity);

        return Inertia::render('Checkout/Index', [
            'cart' => $cart,
            'total' => $total,
            'user' => auth()->user(),
        ]);
    }

    // 送出訂單
    public function store(Request $request)
    {
        $request->validate([
            'recipient_name'    => 'required|string|max:50',
            'recipient_phone'   => 'required|string|max:20',
            'recipient_address' => 'required|string|max:200',
        ]);

        $cart = Cart::where('user_id', auth()->id())
            ->where('status', 'active')
            ->with('items.product')
            ->first();

        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', '購物車是空的');
        }

        $total = $cart->items->sum(fn($item) => $item->price * $item->quantity);

        // 建立訂單
        $order = Order::create([
            'user_id'           => auth()->id(),
            'order_number'      => Order::generateOrderNumber(),
            'status'            => 'pending',
            'total'             => $total,
            'recipient_name'    => $request->recipient_name,
            'recipient_phone'   => $request->recipient_phone,
            'recipient_address' => $request->recipient_address,
            'payment_method'    => 'credit_card',
        ]);

        // 建立訂單明細
        foreach ($cart->items as $item) {
            OrderItem::create([
                'order_id'     => $order->id,
                'product_id'   => $item->product_id,
                'product_name' => $item->product->name,
                'price'        => $item->price,
                'quantity'     => $item->quantity,
            ]);

            // 扣庫存
            $item->product->decrement('stock', $item->quantity);
        }

        // 購物車標記為已結帳
        $cart->update(['status' => 'checked_out']);

        // 導向綠界付款（下一步串接）
        return redirect()->route('checkout.success', $order);
    }

    // 訂單完成頁面
    public function success(Order $order)
    {
        $order->load('items');

        return Inertia::render('Checkout/Success', [
            'order' => $order,
        ]);
    }

    // 下載 PDF 帳單
    public function downloadPdf(Order $order)
    {
        // 確保只能下載自己的訂單
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        $order->load('items', 'user');

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.order', ['order' => $order]);

        return $pdf->download('訂單-' . $order->order_number . '.pdf');
    }
}