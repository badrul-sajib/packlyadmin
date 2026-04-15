<?php

namespace Modules\Api\V1\Ecommerce\Coupon\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ShopCouponProductEligibilityRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product_id'          => 'required|array',
            'product_id.*'        => 'required|exists:products,id|exists:shop_products,product_id',
            'sku'                 => 'nullable|array',
            'sku.*'               => 'nullable|exists:product_variations,sku',
            'quantity'            => 'required|array',
            'quantity.*'          => 'required|numeric|min:1',
            'coupon_code'         => 'nullable|exists:coupons,code',
            'customer_address_id' => 'nullable|exists:customer_addresses,id',
        ];
    }

    public function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'message' => 'The given data was invalid.',
            'errors'  => $validator->errors(),
        ], 422));
    }
} 