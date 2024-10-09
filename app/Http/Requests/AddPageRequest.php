<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class AddPageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'slug' => [
                'required',
                'max:255',
                Rule::unique('pages')->ignore($this->pageId),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'slug.unique' => __('Slug must be unique'),
        ];
    }




    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json(['failedValidation' => $validator->errors()], 422)
        );
    }
}
