<?php

namespace Modules\Api\V1\Merchant\Payment\Http\Requests;

use App\Services\ApiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpFoundation\Response;

class PayoutBeneficiaryRequest extends FormRequest
{
    public function rules(): array
    {
        if ($this->isMethod('post')) {
            return [
                'payout_beneficiary_type_id' => 'required|exists:payout_beneficiary_types,id',
                'payout_beneficiary_mobile_wallet_id' => [
                    'nullable',
                    'required_if:payout_beneficiary_type_id,1',
                    'exists:payout_beneficiary_mobile_wallets,id',
                ],
                'payout_beneficiary_bank_id' => [
                    'nullable',
                    'required_if:payout_beneficiary_type_id,2',
                    'exists:payout_beneficiary_banks,id',
                ],
                'account_holder_name' => 'nullable|string|max:255',
                'account_number' => 'required|numeric',
                'branch_name' => 'nullable|string|max:255',
                'routing_number' => 'nullable|required_if:payout_beneficiary_type_id,2|digits:9',
                'is_default' => 'required|boolean',
            ];
        }

        if (in_array($this->method(), ['PUT', 'PATCH'])) {
            return [
                'payout_beneficiary_type_id' => 'sometimes|required|exists:payout_beneficiary_types,id',
                'payout_beneficiary_mobile_wallet_id' => [
                    'nullable',
                    'required_if:payout_beneficiary_type_id,1',
                    'exists:payout_beneficiary_mobile_wallets,id',
                ],
                'payout_beneficiary_bank_id' => [
                    'nullable',
                    'required_if:payout_beneficiary_type_id,2',
                    'exists:payout_beneficiary_banks,id',
                ],
                'account_holder_name' => 'sometimes|required|string|max:255',
                'account_number' => 'sometimes|required|string|max:255',
                'branch_name' => 'nullable|string|max:255',
                'routing_number' => 'nullable|required_if:payout_beneficiary_type_id,2|digits:9',
                'is_default' => 'required|boolean',
            ];
        }

        return [];
    }

    public function authorize(): bool
    {
        return true;
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            ApiResponse::validationError('Validation Failed', $validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY)
        );
    }
}
