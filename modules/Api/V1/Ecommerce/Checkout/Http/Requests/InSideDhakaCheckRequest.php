<?php

namespace Modules\Api\V1\Ecommerce\Checkout\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class InSideDhakaCheckRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'address' => 'required',
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
