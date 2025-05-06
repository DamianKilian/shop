<?php

namespace App\Http\Controllers;

use App\Integrations\Przelewy24;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class Przelewy24Controller extends Controller
{
    public function __construct()
    {
        // $this->middleware('auth');
    }

    public function transactionRegister(Request $request, Przelewy24 $przelewy24)
    {
        $order = Order::with(['address.country', 'address.areaCode'])
            ->whereId(session('orderId'))
            ->first();
        $przelewy24->transactionRegister($order, !!$request->regulationAccept);
    }

    public function transactionStatus(Request $request)
    {
        Log::debug(json_encode($request->all()));
    }
}
