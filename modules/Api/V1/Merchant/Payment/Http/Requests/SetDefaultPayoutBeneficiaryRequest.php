<?php

namespace Modules\Api\V1\Merchant\Payment\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Services\ApiResponse;

class SetDefaultPayoutBeneficiaryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'payout_recurring_type' => ['required', 'integer',new \Illuminate\Validation\Rules\Enum(\App\Enums\PayoutRecurringTypes::class)],
            'is_default' => ['required', 'boolean'],
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            ApiResponse::validationError(
                'Validation Failed',
                $validator->errors(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            )
        );
    }
}
