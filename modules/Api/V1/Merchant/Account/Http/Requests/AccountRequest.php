<?php

namespace Modules\Api\V1\Merchant\Account\Http\Requests;

use App\Enums\AccountTypes;
use App\Rules\UniqueForMerchant;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class AccountRequest extends FormRequest
{
    public function rules(): array
    {
        if ($this->isMethod('post')) {
            return [
                'name'             => ['required', 'string', 'max:255', new UniqueForMerchant('accounts', 'name')],
                'code'             => ['nullable', 'string', 'max:15'],
                'account_number'   => ['nullable', 'string', new UniqueForMerchant('accounts', 'account_number')],
                'account_type'     => ['required', 'integer', new Enum(AccountTypes::class)],
                'description'      => 'nullable|string|max:255',
                'balance'          => 'nullable|numeric|min:0|max:9999999999999',
                'branch'           => 'nullable|string|max:255',
                'routing_number'   => 'nullable|string|max:255',
            ];
        }

        $account = $this->route('account');

        if (in_array($this->method(), ['PUT', 'PATCH'])) {
            return [
                'name' => [
                    'required',
                    'string',
                    'max:255',
                    new UniqueForMerchant('accounts', 'name', 'id', $account->id),
                ],
                'code' => [
                    'required',
                    'string',
                    'max:15',
                ],
                'account_number' => [
                    'nullable',
                    'string',
                    new UniqueForMerchant('accounts', 'account_number', 'id', $account->id),
                ],
                'description'    => 'nullable|string|max:255',
                'balance'        => 'nullable|numeric|min:0|max:9999999999999',
                'branch'         => 'nullable|string|max:255',
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
