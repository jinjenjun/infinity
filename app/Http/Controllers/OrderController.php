<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
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
            $managedUserIds = User::where('managed_by', $authUser->id)->pluck('id');
            $managedUserIds->push($authUser->id);
            $orders = Order::with(['user', 'items'])
                ->whereIn('user_id', $managedUserIds)
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            $orders = Order::with(['items'])
                ->where('user_id', $authUser->id)
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return Inertia::render('Orders/Index', [
            'orders'       => $orders,
            'isAdmin'      => $authUser->hasAnyRole(['superadmin', 'admin']),
            'isSuperAdmin' => $authUser->hasRole('superadmin'),
        ]);
    }

    public function show(Order $order)
    {
        $authUser = auth()->user();

        if ($authUser->hasRole('superadmin')) {
            // 可以看全部
        } elseif ($authUser->hasRole('admin')) {
            $managedUserIds = User::where('managed_by', $authUser->id)->pluck('id');
            $managedUserIds->push($authUser->id);
            if (!$managedUserIds->contains($order->user_id)) {
                abort(403);
            }
        } else {
            if ($order->user_id !== $authUser->id) {
                abort(403);
            }
        }

        $order->load(['items', 'user']);

        return Inertia::render('Orders/Show', [
            'order'        => $order,
            'isAdmin'      => $authUser->hasAnyRole(['superadmin', 'admin']),
            'isSuperAdmin' => $authUser->hasRole('superadmin'),
        ]);
    }
}