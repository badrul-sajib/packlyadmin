<?php

namespace Modules\Api\V1\Merchant\Auth\Http\Requests;

use App\Services\ApiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpFoundation\Response;

class ResetPasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Set to false if you want to restrict access
    }

    public function rules(): array
    {
        return [
            'phone'                 => 'required|exists:mysql_external.users,phone',
            'password'              => 'required|string|min:8',
            'password_confirmation' => 'required|same:password',
            'otp'                   => 'required',
        ];
    }

    public function messages(): array
    {
        return [
            'phone.exists' => 'The mobile number is not registered with us.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            ApiResponse::validationError('Validation Failed', $validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY)
        );
    }
}
