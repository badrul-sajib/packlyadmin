<?php

namespace Modules\Api\V1\Merchant\Supplier\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DuePaymentRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'supplier_id' => [
                'required',
                Rule::exists('suppliers', 'id')->where(function ($query) {
                    $query->where('merchant_id', auth()->user()->merchant->id);
                }),
            ],
            'from_account_id' => [
                'required',
                Rule::exists('wallets', 'id')->where(function ($query) {
                    $query->where('merchant_id', auth()->user()->merchant->id);
                }),
            ],
            'date'        => 'required|date',
            'amount'      => 'required|numeric|min:1',
            'reference'   => 'nullable|string|max:255',
            'description' => 'nullable|string|max:500',
            'attachment'  => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
