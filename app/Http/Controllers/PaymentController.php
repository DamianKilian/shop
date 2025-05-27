<?php

namespace App\Http\Controllers;

use App\Payment\PaymentManager;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function __construct()
    {
        // $this->middleware('auth');
    }

    public function pay(Request $request, PaymentManager $paymentManager)
    {
        $order = Order::with(['address.country', 'address.areaCode'])
            ->whereId(session('orderId'))
            ->first();
        $paymentManager->pay($order, !!$request->regulationAccept);
    }

    public function transactionStatus(Request $request)
    {
        Log::debug(json_encode($request->all()));
    }
}
