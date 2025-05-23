<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class addOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'price' => ['required', 'regex:/^\d+((\.|\,)\d{1,2})?$/'],
        ];
    }

    public function messages(): array
    {
        return [
            'price.required' => __('Price is required'),
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json(['failedValidation' => $validator->errors()], 422)
        );
    }
}
