<?php

namespace Modules\Api\V1\Ecommerce\Auth\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class SendOtpRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'phone' => ['required', 'regex:/^01[3-9]\d{8}$/'],
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
