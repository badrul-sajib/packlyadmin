<?php

namespace Modules\Api\V1\Merchant\Payment\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PurchasePaymentRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'purchase_id' => ['required', Rule::exists('purchases', 'id')->where('merchant_id', auth()->user()->merchant->id)],
            'wallet_id'   => ['required', Rule::exists('accounts', 'id')->where('merchant_id', auth()->user()->merchant->id)],
            'amount'      => 'required|numeric|min:1',
            'date'        => 'nullable|date',
            'ref_no'      => 'nullable|string|max:255',
            'note'        => 'nullable|string|max:255',
            'attachment'  => 'nullable|mimes:jpeg,png,pdf',
            'supplier_id' => 'required|exists:suppliers,id',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
