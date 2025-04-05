<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddAddressRequest;
use App\Models\Address;
use App\Models\AreaCode;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function addresses(Request $request)
    {
        return view('account.addresses');
    }

    public function getAreaCodes(Request $request)
    {
        $areaCodes = AreaCode::all();
        $defaultAreaCode = AreaCode::whereCode('48')->first();
        return response()->json([
            'areaCodes' => $areaCodes,
            'defaultAreaCode' => $defaultAreaCode,
        ]);
    }

    public function getAddresses(Request $request)
    {
        $addresses = Address::whereUserId(auth()->user()->id)->get();
        return response()->json([
            'addresses' => $addresses,
        ]);
    }

    public function deleteAddresses(Request $request)
    {
        foreach ($request->addresses as $address) {
            $addressIds[] = $address['id'];
        }
        Address::whereUserId(auth()->user()->id)
            ->whereIn('id', $addressIds)
            ->delete();
    }

    public function addAddress(AddAddressRequest $request)
    {
        $addressData = [
            "email" => $request->email,
            "area_code_id" => $request->area_code_id,
            "phone" => $request->phone,
            "name" => $request->name,
            "surname" => $request->surname,
            "nip" => $request->nip,
            "company_name" => $request->company_name,
            "street" => $request->street,
            "house_number" => $request->house_number,
            "apartment_number" => $request->apartment_number,
            "postal_code" => $request->postal_code,
            "city" => $request->city,
        ];
        if ($request->addressId) {
            $address = Address::whereId($request->addressId)
                ->whereUserId(auth()->user()->id)
                ->first();
            $address->update($addressData);
        } else {
            $addressData['user_id'] = auth()->user()->id;
            $address = Address::create($addressData);
        }
    }
}
