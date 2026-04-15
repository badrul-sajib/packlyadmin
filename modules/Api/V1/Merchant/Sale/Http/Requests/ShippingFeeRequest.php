<?php

namespace Modules\Api\V1\Merchant\Sale\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ShippingFeeRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'sell_product_id' => 'required|integer|exists:sell_products,id',
            'shipping_fee'    => 'required|numeric',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
