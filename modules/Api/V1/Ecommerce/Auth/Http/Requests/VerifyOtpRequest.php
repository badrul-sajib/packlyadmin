<?php

namespace Modules\Api\V1\Ecommerce\Auth\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class VerifyOtpRequest extends FormRequest
{
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
            validationError('Validation Error', $validator->errors())
        );
    }

    public function authorize(): bool
    {
        return true;
    }
}
