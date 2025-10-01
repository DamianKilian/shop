<?php

namespace App\Http\Requests\Account;

use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Hash;

class UpdatePasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'current_password' => [
                'required',
                function (string $attribute, mixed $value, Closure $fail) {
                    $user = auth()->user();
                    $passwordNewHash = $user->password;
                    if (!Hash::check($value, $passwordNewHash)) {
                        $fail("Password is incorrect.");
                    }
                },
            ],
            'password' => ['required', 'string', 'min:8', 'confirmed',],
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json(['failedValidation' => $validator->errors()], 422)
        );
    }
}
