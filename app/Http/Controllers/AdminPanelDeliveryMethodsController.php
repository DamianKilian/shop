<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddDeliveryMethodRequest;
use App\Models\DeliveryMethod;
use Illuminate\Http\Request;

class AdminPanelDeliveryMethodsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function deliveryMethods()
    {
        return view('adminPanel.delivery-methods', []);
    }

    public function addDeliveryMethod(AddDeliveryMethodRequest $request)
    {
        $data = [
            "name" => $request->name,
            "price" => str_replace(',', '.', $request->price),
            "active" => isset($request->active),
            "description" => $request->description,
        ];
        if ($request->deliveryMethodId) {
            $deliveryMethod = DeliveryMethod::whereId($request->deliveryMethodId)->first();
            $deliveryMethod->update($data);
        } else {
            DeliveryMethod::create($data);
        }
    }

    public function getDeliveryMethods()
    {
        $deliveryMethods = DeliveryMethod::all()->keyBy('id');
        return response()->json([
            'deliveryMethods' => $deliveryMethods,
        ]);
    }

    public function deleteDeliveryMethods(Request $request)
    {
        foreach ($request->deliveryMethods as $deliveryMethod) {
            $deliveryMethodIds[] = $deliveryMethod['id'];
        }
        DeliveryMethod::whereIn('id', $deliveryMethodIds)->delete();
    }
}
