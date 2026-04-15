<?php

namespace Modules\Api\V1\Merchant\Auth\Http\Requests;

use App\Services\ApiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpFoundation\Response;

class OtpValidationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Set to false if you want to restrict access
    }

    public function rules(): array
    {
        return [
            'phone' => 'required|exists:otps,phone',
            'otp'   => 'required|exists:otps,otp',
        ];
    }

    public function messages(): array
    {
        return [
            'phone.exists' => 'Invalid phone number.',
            'otp.exists'   => 'Invalid OTP.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            ApiResponse::validationError('Validation Failed', $validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY)
        );
    }
}
