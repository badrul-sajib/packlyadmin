<?php

namespace Modules\Api\V1\Merchant\Account\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IncomeRequest extends FormRequest
{
    public function rules(): array
    {
        $merchantId = auth()->user()->merchant->id;

        return [
            'from_account_id' => ['required', Rule::exists('accounts', 'id')->where('merchant_id', $merchantId)],
            'amount'          => 'required|numeric|min:1',
            'date'            => 'nullable|date',
            'reason'          => 'nullable|string|max:255',
            'reference'       => 'nullable|string|max:255',
            'description'     => 'nullable|string|max:500',
            'attachment'      => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'from_account_id.exists' => 'The selected from account does not exist',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
