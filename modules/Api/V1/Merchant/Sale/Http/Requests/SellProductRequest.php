<?php

namespace Modules\Api\V1\Merchant\Sale\Http\Requests;

use App\Enums\AccountTypes;
use App\Services\ApiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Validation\Rule;

class SellProductRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        if ($this->account_id == 0) {
            $this->merge([
                'account_id' => null
            ]);
        }
    }
    public function rules(): array
    {
        return [
            'customer_id'                 => 'required|integer',
            'invoice_no'                  => 'nullable|integer',
            'sale_date'                   => 'nullable|integer',
            'due_date'                    => 'nullable|integer',
            'total_item'                  => 'nullable|integer',
            'total_discount_percentage'   => 'nullable|numeric',
            'total_discount_amount'       => 'nullable|numeric',
            'total_sale_vat_percentage'   => 'nullable|numeric',
            'total_sale_vat_amount'       => 'nullable|numeric',
            'total_shipping_cost'         => 'nullable|numeric|max:20000',
            'total_amount'                => 'nullable|numeric',
            'grand_total'                 => 'nullable|numeric',
            'product_discount_percentage' => 'nullable|numeric',
            'sub_total'                   => 'nullable|numeric',
            'products'                    => 'required|json',
            'sold_from'                   => 'required|string|in:regular,pos',
            'account_id'                  => [
                'nullable',
                Rule::exists('accounts', 'id')
                    ->whereIn('account_type', [AccountTypes::CASH, AccountTypes::BANK])
                    ->where('merchant_id', auth()->user()->merchant->id)
            ]
        ];
    }

    public function messages(): array
    {
        return [
            'total_shipping_cost.max' => 'Shipping cost is too much',
        ];
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
