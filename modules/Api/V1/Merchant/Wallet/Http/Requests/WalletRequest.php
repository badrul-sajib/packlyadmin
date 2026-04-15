<?php

namespace Modules\Api\V1\Merchant\Wallet\Http\Requests;

use App\Rules\UniqueForMerchant;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class WalletRequest extends FormRequest
{
    public function rules(): array
    {
        if ($this->isMethod('post')) {
            return [
                'wallet_type' => [
                    'required',
                    'integer',
                    'in:1,2',
                    function ($attribute, $value, $fail) {
                        $merchantId   = Auth::user()->merchant->id;
                        $walletExists = DB::table('wallets')
                            ->where('merchant_id', $merchantId)
                            ->where('wallet_type', $value)
                            ->exists();

                        if ($value == 1 && $walletExists) {
                            $fail('You can only have one petty cash account.');
                        }
                    },
                ],
                'name'              => ['required', 'string', 'max:255', new UniqueForMerchant('wallets', 'name')],
                'account_number'    => ['nullable', 'string', 'max:50', new UniqueForMerchant('wallets', 'account_number')],
                'bank_name'         => ['nullable', 'string', 'max:100'],
                'branch_name'       => ['nullable', 'string', 'max:100'],
                'route_no'          => ['nullable', 'string', 'max:50', new UniqueForMerchant('wallets', 'route_no')],
                'available_balance' => ['nullable', 'string'],
            ];
        }

        if (in_array($this->method(), ['PUT', 'PATCH'])) {
            return [
                'wallet_type' => [
                    'required',
                    'integer',
                    'in:1,2',
                    Rule::unique('wallets', 'wallet_type')
                        ->where(function ($query) {
                            $query->where('merchant_id', Auth::user()->merchant->id)
                                ->where('wallet_type', 1);
                        })
                        ->ignore($this->route('wallet')),
                ],
                'name'              => ['required', 'string', 'max:255', new UniqueForMerchant('wallets', 'name', 'id', $this->route('id'))],
                'account_number'    => ['nullable', 'string', 'max:50', new UniqueForMerchant('wallets', 'account_number', 'id', $this->route('id'))],
                'bank_name'         => ['nullable', 'string', 'max:100'],
                'branch_name'       => ['nullable', 'string', 'max:100'],
                'route_no'          => ['nullable', 'string', 'max:50', new UniqueForMerchant('wallets', 'route_no', 'id', $this->route('id'))],
                'available_balance' => ['nullable', 'string'],
            ];
        }

        return [];
    }

    public function messages(): array
    {
        return [
            'wallet_type.unique' => 'You can only have one petty cash account.',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
