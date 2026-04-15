<?php

namespace Modules\Api\V1\Ecommerce\Customer\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class PaymentSuccessRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'amount'  => 'required|numeric',
            'tran_id' => 'required|integer|unique:e_payments,order_id',
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
