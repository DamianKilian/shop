<?php

namespace App\Http\Controllers\Account;

use App\Http\Requests\AddAddressRequest;
use App\Models\Address;
use App\Models\AreaCode;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Country;

class AddressController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function setDefaultAddress(Request $request)
    {
        $user = auth()->user();
        if ($request->defaultAddressId) {
            $user->default_address_id = $request->defaultAddressId;
        } elseif ($request->defaultAddressInvoiceId) {
            $user->default_address_invoice_id = $request->defaultAddressInvoiceId;
        }
        $user->save();
    }

    public function addresses(Request $request)
    {
        return view('account.addresses');
    }

    public function getAreaCodes(Request $request)
    {
        $areaCodes = AreaCode::all();
        $defaultAreaCode = AreaCode::whereCode('48')->first();
        $countries = Country::all(['id', 'code', 'name'])->keyBy('id');
        $defaultCountry = Country::whereCode('PL')->first();
        return response()->json([
            'areaCodes' => $areaCodes,
            'defaultAreaCode' => $defaultAreaCode,
            'countries' => $countries,
            'defaultCountry' => $defaultCountry,
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
        $user = auth()->user();
        $defaultAddressId = $user->default_address_id;
        $defaultAddressInvoiceId = $user->default_address_invoice_id;
        foreach ($request->addresses as $address) {
            $addressIds[] = $address['id'];
            if ((int)$address['id'] === $defaultAddressId) {
                $user->default_address_id = null;
            }
            if ((int)$address['id'] === $defaultAddressInvoiceId) {
                $user->default_address_invoice_id = null;
            }
        }
        $user->save();
        Address::whereUserId($user->id)
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
            "country_id" => $request->country_id,
        ];
        $user = auth()->user();
        if ($request->addressId) {
            $address = Address::whereId($request->addressId)
                ->whereUserId($user->id)
                ->first();
            $address->update($addressData);
        } else {
            $addressData['user_id'] = $user->id;
            $address = Address::create($addressData);
            $user->default_address_id = $address->id;
            $user->default_address_invoice_id = $address->id;
            $user->save();
            return response()->json([
                'newAddressId' => $address->id,
            ]);
        }
    }
}
