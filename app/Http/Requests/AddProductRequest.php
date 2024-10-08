<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class AddProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|max:255',
            'slug' => [
                'required',
                'max:255',
                Rule::unique('products')->ignore($this->productId),
            ],
            'price' => ['required', 'regex:/^\d+((\.|\,)\d{1,2})?$/'],
            'quantity' => 'required|integer',
            'description' => 'required',
            'categoryId' => 'required',
            'files.*' => 'nullable|mimes:jpg,jpeg,png,bmp,gif,webp|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => __('Title is required'),
            'slug.unique' => __('Slug must be unique'),
            'price.required' => __('Price is required'),
            'price.regex' => __('The price is invalid'),
            'quantity.required' => __('Quantity is required'),
            'description.required' => __('Description is required'),
            'categoryId.required' => __('Category is required'),
            'files.*' => [
                'mimes' => __('Accepted types are: jpg, jpeg, png, bmp, gif, webp'),
                'max' => __('Max file size is 2MB'),
            ],
        ];
    }




    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json(['failedValidation' => $validator->errors()], 422)
        );
    }
}
