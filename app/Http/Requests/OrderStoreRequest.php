<?php

namespace App\Http\Requests;

use App\Models\Address;
use Illuminate\Foundation\Http\FormRequest;

class OrderStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'deliveryMethod' => 'required',
        ];
        return $rules
            + Address::createRules('address.')
            + Address::createRules('addressInvoice.', 'required_if:addressInvoiceTheSame,false');
    }

    public function messages(): array
    {
        return [
            'deliveryMethod.required' => __('The delivery method is required'),
        ];
    }
}
