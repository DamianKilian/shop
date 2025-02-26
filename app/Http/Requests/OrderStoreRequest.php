<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'deliveryMethod' => 'required',
        ];
    }

    public function messages(): array
    {
        return [
            'deliveryMethod.required' => __('The delivery method is required'),
        ];
    }
}
