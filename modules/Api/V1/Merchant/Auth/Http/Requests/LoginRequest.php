<?php

namespace Modules\Api\V1\Merchant\Auth\Http\Requests;

use App\Services\ApiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpFoundation\Response;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Set to false if you want to restrict access
    }

    public function rules(): array
    {
        return [
            'phone'    => 'required_without:token|nullable',
            'password' => 'required_if:token,null|nullable',
            'token'    => 'required_if:phone,null|nullable',
        ];
    }

    public function messages(): array
    {
        return [
            'phone.required_without' => 'Phone number is required',
            'password.required_if'   => 'Password is required',
            'token.required_if'      => 'Token is required',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            ApiResponse::validationError('Validation Failed', $validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY)
        );
    }
}
