<?php

namespace Modules\Api\V1\Merchant\Account\Http\Requests;

use App\Rules\UniqueForMerchant;
use Illuminate\Foundation\Http\FormRequest;

class BeneficiaryAccountRequest extends FormRequest
{
    public function rules(): array
    {
        if ($this->isMethod('post')) {
            return [
                'type'           => 'required|in:1,2,3',
                'name'           => ['required', 'string', 'max:255', new UniqueForMerchant('beneficiary_accounts', 'name')],
                'account_number' => ['required', 'string', 'max:255', new UniqueForMerchant('beneficiary_accounts', 'account_number')],
                'bank_name'      => 'nullable|string|max:255',
                'branch_name'    => 'nullable|string|max:255',
                'routing_number' => 'nullable|string|max:255',
            ];
        }

        $account = $this->route('beneficiary_account');

        if (in_array($this->method(), ['PUT', 'PATCH'])) {
            return [
                'type'           => 'required|in:1,2,3',
                'name'           => ['required', 'string', 'max:255', new UniqueForMerchant('beneficiary_accounts', 'name', 'id', $account)],
                'account_number' => ['required', 'string', 'max:255', new UniqueForMerchant('beneficiary_accounts', 'account_number', 'id', $account)],
                'bank_name'      => 'nullable|string|max:255',
                'branch_name'    => 'nullable|string|max:255',
                'routing_number' => 'nullable|string|max:255',
            ];
        }

        return [];
    }

    public function authorize(): bool
    {
        return true;
    }
}
