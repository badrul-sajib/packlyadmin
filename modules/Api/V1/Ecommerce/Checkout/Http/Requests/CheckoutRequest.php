<?php

namespace Modules\Api\V1\Ecommerce\Checkout\Http\Requests;

use App\Enums\DeliveryType;
use App\Enums\PaymentMethod;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class CheckoutRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $deliveryTypes = implode(',', array_column(DeliveryType::cases(), 'value'));
        $methods       = implode(',', array_column(PaymentMethod::cases(), 'value'));

        return [
            'customer_address_id' => 'required|exists:customer_addresses,id',
            'delivery_type'       => 'required|array',
            'delivery_type.*'     => "required|in:$deliveryTypes",
            'payment_method'      => "required|in:$methods",
            'product_id'          => 'required|array',
            'product_id.*'        => 'required|exists:products,id|exists:shop_products,product_id',
            'sku'                 => 'nullable|array',
            'sku.*'               => 'nullable|exists:product_variations,sku',
            'quantity'            => 'required|array',
            'quantity.*'          => 'required|numeric|min:1',
            // 'shipping_type' => 'required',
            'coupon_code'         => 'nullable|exists:coupons,code',
            'order_from'          => 'nullable|in:1,2', // 1 = app, 2 = web
            'final_payable_price' => 'required|numeric|min:0',

            'utm_source'          => 'nullable|max:500',
            'utm_medium'          => 'nullable|max:500',
            'utm_campaign'        => 'nullable|max:500',
            'utm_term'            => 'nullable|max:500',
            'utm_content'         => 'nullable|max:100',
            'utm_id'              => 'nullable|max:500',

        ];
    }

    public function messages(): array
    {
        return [
            'customer_address_id.required' => 'Please select an address',
            'customer_address_id.exists'   => 'The selected address is invalid',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'message' => 'The given data was invalid.',
            'errors'  => $validator->errors(),
        ], 422));
    }
}
