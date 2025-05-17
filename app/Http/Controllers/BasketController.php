<?php

namespace App\Http\Controllers;

use App\Services\BasketService;
use Illuminate\Http\Request;
use App\Http\Requests\OrderStoreRequest;
use App\Models\Address;
use App\Models\DeliveryMethod;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BasketController extends Controller
{
    public function __construct()
    {
        // $this->middleware('auth');
    }

    public function basketIndex()
    {
        $deliveryMethods = DeliveryMethod::whereActive(true)->get()->keyBy('id');
        return view('basket.index', [
            'deliveryMethods' => json_encode($deliveryMethods),
        ]);
    }

    public function getProductsInBasketData(Request $request)
    {
        $productsInBasketData = BasketService::getProductsInBasketData($request->productsInBasket);
        BasketService::productsInBasketDataForList($productsInBasketData);
        return response()->json([
            'productsInBasketData' => $productsInBasketData,
        ]);
    }

    public function getBasketSummary(Request $request)
    {
        $deliveryMethod = DeliveryMethod::whereId($request->deliveryMethodId)->first();
        $summary = BasketService::getBasketSummary($request->productsInBasket, $deliveryMethod);
        return response()->json([
            'basketLastChange' => $request->basketLastChange,
            'summary' => $summary['formatted'],
        ]);
    }

    public function orderStore(OrderStoreRequest $request)
    {
        $productsInBasketArr = json_decode($request->productsInBasket, true);
        $deliveryMethod = DeliveryMethod::whereId($request->deliveryMethodId)->first();
        $summary = BasketService::getBasketSummary($productsInBasketArr, $deliveryMethod);
        $order = new Order;
        $addressIds = [];
        DB::transaction(function () use ($order, $summary, $request, $productsInBasketArr, $deliveryMethod, &$addressIds) {
            $order->session_Id = Str::random(100);
            $order->price = $summary['raw']['totalPrice'];
            $order->delivery_price = $deliveryMethod->price;
            $order->delivery_method_id = $request->deliveryMethodId;
            $order->user_id = Auth::check() ? auth()->user()->id : null;
            $address = $this->createAddress($request->address);
            $order->address_id = $address->id;
            $addressIds['address'] = $address->id;
            if ('false' === $request->addressInvoiceTheSame) {
                $addressInvoice = $this->createAddress($request->addressInvoice);
                $order->address_invoice_id = $addressInvoice->id;
                $addressIds['addressInvoice'] = $addressInvoice->id;
            }
            $order->save();
            $order->products()->attach($productsInBasketArr);
        });
        $this->setSession($summary, $productsInBasketArr, $deliveryMethod, $order->id, $addressIds);
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
            "country_id" => $data['country_id'],
        ]);
    }

    protected function setSession($summary, $productsInBasketArr, $deliveryMethod, $orderId, $addressIds)
    {
        session([
            'summary' => $summary,
            'productsInBasketArr' => $productsInBasketArr,
            'deliveryMethod' => json_encode($deliveryMethod),
            'orderId' => $orderId,
            'addressIds' => $addressIds,
        ]);
    }

    public function orderPayment(int $orderId)
    {
        if ($orderId !== session()->get('orderId')) {
            if (Auth::guest()) {
                return redirect()->route('login');
            }
            $order = Order::whereId($orderId)
                ->whereUserId(auth()->user()->id)
                ->with('products')
                ->with('deliveryMethod')
                ->first();
            if (!$order) {
                abort(403);
            }
            $addressIds['address'] = $order->address_id;
            if ($order->address_invoice_id) {
                $addressIds['addressInvoice'] = $order->address_invoice_id;
            }
            $productsInBasket = DB::table('order_product')->whereOrderId($order->id)->get(['product_id', 'num'])->keyBy('product_id');
            $productsInBasketArr = $productsInBasket->map(fn($row) => (array)$row)->all();
            $deliveryMethod = $order->deliveryMethod;
            $summary = BasketService::getBasketSummary($productsInBasketArr, $deliveryMethod);
            $this->setSession($summary, $productsInBasketArr, $deliveryMethod, $order->id, $addressIds);
        } else {
            $productsInBasketArr = session()->get('productsInBasketArr');
            $summary = session()->get('summary');
            $deliveryMethod = session()->get('deliveryMethod');
            $addressIds = session()->get('addressIds');
        }
        $addressesDb = Address::whereIn('id', array_values($addressIds))->with(['areaCode', 'country'])->get()->keyBy('id');
        $addresses = [];
        foreach ($addressIds as $key => $addressId) {
            $addresses[$key] = $addressesDb[$addressId];
        }
        $productsInBasketData = BasketService::getProductsInBasketData($productsInBasketArr);
        BasketService::productsInBasketDataForList($productsInBasketData);
        return view('basket.payment', [
            'productsInBasketData' => $productsInBasketData->toJson(),
            'summary' => json_encode($summary['formatted']),
            'deliveryMethod' => $deliveryMethod,
            'addresses' => json_encode($addresses),
        ]);
    }

    public function orderCompleted(Request $request, int $orderId)
    {
        return view('basket.completed', []);
    }
}
