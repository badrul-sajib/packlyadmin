<?php

namespace Modules\Api\V1\Ecommerce\Auth\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class NewPasswordRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'password'         => 'required|min:8|max:30|string',
            'confirm_password' => 'required|same:password',
        ];
    }

    public function messages(): array
    {
        return [
            'password.min'          => 'Password must be at least 8 characters long.',
            'confirm_password.same' => 'Password and confirmation password do not match.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            validationError('Validation Error', $validator->errors())
        );
    }

    public function authorize(): bool
    {
        return true;
    }
}
