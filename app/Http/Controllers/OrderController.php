<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderStoreRequest;
use App\Models\Address;
use App\Models\Order;
use App\Services\BasketService;
use App\Services\DeliveryMethodsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class OrderController extends Controller
{
    public function __construct()
    {
        // $this->middleware('auth');
    }

    public function orderStore(OrderStoreRequest $request, DeliveryMethodsService $deliveryMethodsService)
    {
        $productsInBasketArr = json_decode($request->productsInBasket, true);
        $summary = BasketService::getBasketSummary($productsInBasketArr, $request->deliveryMethod, $deliveryMethodsService);
        $deliveryMethod = json_encode($deliveryMethodsService->deliveryMethods[$request->deliveryMethod]);
        $order = new Order;
        DB::transaction(function () use ($order, $summary, $request, $productsInBasketArr) {
            $order->price = $summary['raw']['totalPrice'];
            $order->delivery_method = $request->deliveryMethod;
            $order->user_id = Auth::check() ? auth()->user()->id : null;
            $address = $this->createAddress($request->address);
            $order->address_id = $address->id;
            if ('false' === $request->addressInvoiceTheSame) {
                $addressInvoice = $this->createAddress($request->addressInvoice);
                $order->address_invoice_id = $addressInvoice->id;
            }
            $order->save();
            $order->products()->attach($productsInBasketArr);
        });
        session([
            'summary' => $summary,
            'productsInBasketArr' => $productsInBasketArr,
            'deliveryMethod' => $deliveryMethod,
            'orderPaymentAccess' => $order->id,
        ]);
        return redirect()->route('order-payment', ['order' => $order->id]);
    }

    protected function createAddress($data)
    {
        return Address::create([
            "email" => $data['email'],
            "name" => $data['name'],
            "surname" => $data['surname'],
            "nip" => $data['nip'],
            "company_name" => $data['company_name'],
            "phone" => $data['phone'],
            "street" => $data['street'],
            "house_number" => $data['house_number'],
            "apartment_number" => $data['apartment_number'],
            "postal_code" => $data['postal_code'],
            "city" => $data['city'],
            "area_code_id" => $data['area_code_id'],
        ]);
    }

    public function orderPayment(Request $request, DeliveryMethodsService $deliveryMethodsService, int $orderId)
    {
        if ($orderId !== session()->get('orderPaymentAccess')) {
            $order = Order::whereId($orderId)->with('products')->first();
            $productsInBasket = DB::table('order_product')->whereOrderId($order->id)->get(['product_id', 'num'])->keyBy('product_id');
            $productsInBasketArr = $productsInBasket->map(fn($row) => (array)$row)->all();
            $summary = BasketService::getBasketSummary($productsInBasketArr, $order->delivery_method, $deliveryMethodsService);
            $deliveryMethod = json_encode($deliveryMethodsService->deliveryMethods[$order->delivery_method]);
            if (Auth::guest()) {
                return redirect()->route('login');
            }
            if (!Gate::allows('orderPaymentAccess', $order)) {
                abort(403);
            }
        } else {
            $productsInBasketArr = session()->get('productsInBasketArr');
            $summary = session()->get('summary');
            $deliveryMethod = session()->get('deliveryMethod');
        }
        $productsInBasketData = BasketService::getProductsInBasketData($productsInBasketArr);
        BasketService::productsInBasketDataForList($productsInBasketData);
        return view('basket.payment', [
            'productsInBasketData' => $productsInBasketData->toJson(),
            'summary' => json_encode($summary['formatted']),
            'deliveryMethod' => $deliveryMethod,
        ]);
    }
}
