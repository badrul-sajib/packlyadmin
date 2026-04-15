<?php

namespace Modules\Api\V1\Merchant\Payment\Http\Requests;

use App\Enums\AccountTypes;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SellPaymentRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'sell_product_id' => ['required', Rule::exists('sell_products', 'id')->where('merchant_id', auth()->user()->merchant->id)],
            'account_id'      => ['required', Rule::exists('accounts', 'id')->whereIn('account_type', [AccountTypes::CASH, AccountTypes::BANK])->where('merchant_id', auth()->user()->merchant->id)],
            'amount'          => 'required|numeric|min:0',
            'date'            => 'nullable|date',
            'ref_no'          => 'nullable|string|max:255',
            'note'            => 'nullable|string|max:255',
            'attachment'      => 'nullable|mimes:jpeg,png,pdf',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
