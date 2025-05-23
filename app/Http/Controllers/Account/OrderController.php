<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Models\Order;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function orders()
    {
        return view('account.orders');
    }

    public function getOrders()
    {
        $orders = Order::whereUserId(auth()->user()->id)
            ->with('products')
            ->select(['id', 'created_at'])
            ->paginate(20);
        foreach ($orders as &$order) {
            $order->date = date_format($order->created_at, "d-m-Y");
            unset($order->created_at);
            $order->productsList = $order->products->implode('title', ', ');
            unset($order->products);
            $order->orderPaymentUrl = route('order-payment', ['order' => $order->id]);
        }
        return response()->json([
            'orders' => $orders,
        ]);
    }
}
