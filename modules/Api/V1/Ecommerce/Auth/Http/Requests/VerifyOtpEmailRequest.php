<?php

namespace Modules\Api\V1\Ecommerce\Auth\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class VerifyOtpEmailRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'otp'   => 'required|exists:otps,otp',
        ];
    }

    public function messages(): array
    {
        return [
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
