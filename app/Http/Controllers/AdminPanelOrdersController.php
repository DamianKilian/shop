<?php

namespace App\Http\Controllers;

use App\Http\Requests\addOrderRequest;
use App\Models\Order;
use App\Models\OrderStatus;

class AdminPanelOrdersController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function orders()
    {
        return view('adminPanel.orders');
    }

    public function getOrders()
    {
        $orders = Order::with('products')
            ->select(['id', 'price', 'order_status_id', 'created_at'])
            ->paginate(50);
        foreach ($orders as &$order) {
            $order->date = date_format($order->created_at, "d-m-Y");
            unset($order->created_at);
            $order->priceAndCurr = $order->price . ' zł';
            $order->productsList = $order->products->implode('title', ', ');
            unset($order->products);
            $order->orderPaymentUrl = route('order-payment', ['order' => $order->id]);
        }
        return response()->json([
            'orders' => $orders,
        ]);
    }

    public function getOrderData()
    {
        $orderStatuses = OrderStatus::all()->keyBy('id');
        return response()->json([
            'orderStatuses' => $orderStatuses,
        ]);
    }

    public function addOrder(addOrderRequest $request)
    {
        $data = [
            "price" => str_replace(',', '.', $request->price),
            "order_status_id" => $request->orderStatusId,
        ];
        if ($request->orderId) {
            $order = Order::whereId($request->orderId)->first();
            $order->update($data);
        } else {
            $data['created_in_admin_panel'] = true;
            Order::create($data);
        }
    }
}
