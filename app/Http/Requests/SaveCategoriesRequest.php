<?php

namespace App\Http\Requests;

use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;

class SaveCategoriesRequest extends FormRequest
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
    public function rules(Request $request): array
    {
        return [
            'categories.*.*' => [
                function (string $attribute, mixed $subCategory, Closure $fail) use ($request) {
                    $explodeAttr = explode('.', $attribute);
                    $subCategories = $request->categories[$explodeAttr[1]];
                    $namesArr = [];
                    foreach ($subCategories as $subCat) {
                        $subCatName = trim($subCat['name']);
                        if (isset($namesArr[$subCatName])) {
                            $namesArr[$subCatName] += 1;
                        } else {
                            $namesArr[$subCatName] = 1;
                        }
                    }
                    $subCategoryName = trim($subCategory['name']);
                    if (1 < $namesArr[$subCategoryName]) {
                        $fail(__('Category name is repeated'));
                    }
                },
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
