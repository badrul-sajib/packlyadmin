<?php

namespace Modules\Api\V1\Merchant\Auth\Http\Requests;

use App\Services\ApiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpFoundation\Response;

class OtpRegisterVerifyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Set to false if you want to restrict access
    }

    public function rules(): array
    {
        return [
            'otp_type' => 'required|in:email,phone',
            'phone'    => 'required_if:otp_type,phone|exists:otps,phone',
            'email'    => 'required_if:otp_type,email|email|exists:otps,email',
            'otp'      => 'required|exists:otps,otp',
        ];
    }

    public function messages(): array
    {
        return [
            'otp_type.in'  => 'Invalid OTP type. Must be email or phone.',
            'phone.exists' => 'Invalid phone number.',
            'email.exists' => 'Invalid email address.',
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
