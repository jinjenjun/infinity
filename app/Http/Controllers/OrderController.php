<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Inertia\Inertia;

class OrderController extends Controller
{
    public function index()
    {
        $authUser = auth()->user();

        if ($authUser->hasRole('superadmin')) {
            $orders = Order::with(['user', 'items'])
                ->orderBy('created_at', 'desc')
                ->get();
        } elseif ($authUser->hasRole('admin')) {
            $orders = Order::with(['user', 'items.product'])
                ->where(fn ($q) => $q->where('user_id', $authUser->id)
                    ->orWhereHas('items.product', fn ($p) => $p->where('admin_id', $authUser->id)))
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(fn (Order $order) => $this->scopeToSeller($order, $authUser->id));
        } else {
            $orders = Order::with(['items'])
                ->where('user_id', $authUser->id)
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return Inertia::render('Orders/Index', [
            'orders' => $orders,
            'isAdmin' => $authUser->hasAnyRole(['superadmin', 'admin']),
            'isSuperAdmin' => $authUser->hasRole('superadmin'),
        ]);
    }

    public function show(Order $order)
    {
        $authUser = auth()->user();

        if ($authUser->hasRole('superadmin')) {
            // 可以看全部
        } elseif ($authUser->hasRole('admin')) {
            $isBuyer = $order->user_id === $authUser->id;
            $isSeller = $order->items()->whereHas('product', fn ($p) => $p->where('admin_id', $authUser->id))->exists();
            if (! $isBuyer && ! $isSeller) {
                abort(403);
            }
        } else {
            if ($order->user_id !== $authUser->id) {
                abort(403);
            }
        }

        $order->load(['items.product', 'user']);

        if ($authUser->hasRole('admin')) {
            $order = $this->scopeToSeller($order, $authUser->id);
        }

        return Inertia::render('Orders/Show', [
            'order' => $order,
            'isAdmin' => $authUser->hasAnyRole(['superadmin', 'admin']),
            'isSuperAdmin' => $authUser->hasRole('superadmin'),
        ]);
    }

    // 賣家視角：只保留自己的商品明細，total 換成這部分的小計；買家本人看全部
    private function scopeToSeller(Order $order, int $adminId): Order
    {
        if ($order->user_id === $adminId) {
            return $order;
        }

        $items = $order->items
            ->filter(fn ($item) => $item->product?->admin_id === $adminId)
            ->values();

        $items->each->unsetRelation('product');

        $order->setRelation('items', $items);
        $order->total = $items->sum(fn ($item) => $item->price * $item->quantity);
        $order->is_partial = true;

        return $order;
    }
}
