<?php

namespace Modules\Api\V1\Merchant\Purchase\Http\Requests;

use App\Services\ApiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class PurchaseRequest extends FormRequest
{
    public function rules(): array
    {
        $merchantId = optional($this->user()->merchant)->id;

        return [
            'supplier_id' => [
                'required',
                'integer',
                Rule::exists('suppliers', 'id')->where(function ($query) use ($merchantId) {
                    $query->where('merchant_id', $merchantId);
                }),
            ],
            'warehouse_id' => [
                'required',
                'integer',
                Rule::exists('warehouses', 'id')->where(function ($query) use ($merchantId) {
                    $query->where('merchant_id', $merchantId);
                }),
            ],
            'account_id' => [
                'integer',
                Rule::exists('accounts', 'id')
                    ->where(function ($query) use ($merchantId) {
                        $query->where('merchant_id', $merchantId);
                    })
                    ->when(request('current_paid_amount') > 0, function ($rules) {
                        return ['required'];
                    }),
            ],
            'ref_no' => 'nullable|string',
            'note' => 'nullable|string|max:255',
            'purchase_status_id' => 'nullable|integer',
            'purchase_date' => 'nullable|date',
            'attachment' => 'nullable|file|mimes:jpeg,png,pdf',
            'total_item' => 'nullable|integer',
            'total_discount_percentage' => 'nullable|numeric',
            'total_discount_amount' => 'nullable|numeric',
            'total_purchase_vat_percentage' => 'nullable|integer',
            'total_purchase_vat_amount' => 'nullable|numeric',
            'total_shipping_cost' => 'nullable|numeric',
            'total_amount' => 'nullable|numeric',
            'sub_total' => 'nullable|numeric',
            'grand_total' => 'nullable|numeric',
            'purchase_qty' => 'nullable|integer',
            'products' => 'required|json',
            'current_paid_amount' => 'nullable|numeric|min:0',
            'payment_date' => 'nullable|date',
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
