<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $recentOrders = $user->orders()->latest('placed_at')->limit(3)->get();
        $addressesCount = $user->addresses()->count();
        $wishlistCount = $user->wishlists()->count();

        return view('account.dashboard', compact('user', 'recentOrders', 'addressesCount', 'wishlistCount'));
    }

    public function orders(Request $request)
    {
        $orders = $request->user()->orders()->latest('placed_at')->paginate(10);

        return view('account.orders', compact('orders'));
    }

    public function orderShow(Request $request, Order $order)
    {
        $this->authorize('view', $order);

        $order->load(['items', 'payment', 'shippingMethod']);

        return view('account.order-show', compact('order'));
    }
}
