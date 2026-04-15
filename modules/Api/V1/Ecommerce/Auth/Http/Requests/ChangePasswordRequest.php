<?php

namespace Modules\Api\V1\Ecommerce\Auth\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ChangePasswordRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'password_type'    => 'nullable|string',
            'current_password' => $this->input('password_type') != 'new' ? 'required|current_password' : 'nullable',
            'password'         => 'required|string|min:8|max:30|confirmed',
        ];
    }

    public function messages(): array
    {
        return [
            'current_password.current_password'  => 'Current password is wrong.',
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
