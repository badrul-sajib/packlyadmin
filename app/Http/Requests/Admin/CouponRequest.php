<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CouponRequest extends FormRequest
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
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        // Get the coupon ID for updates (this will be passed from the route)
        $id = $this->route('coupon');

        $rules = [
            'name'                 => ['required', 'string', 'max:255', Rule::unique('coupons', 'name')->ignore($id)],
            'code'                 => ['required', 'string', 'max:50', Rule::unique('coupons', 'code')->ignore($id)],
            'discount_value'       => ['required', 'numeric', 'min:1'],
            'max_discount_value'   => ['nullable', 'numeric', 'gte:discount_value'],
            'description'          => ['required', 'string', 'max:255'],
            'min_purchase'         => ['required', 'numeric', 'min:0'],
            'max_purchase'         => ['nullable'],
            'usage_limit_total'    => ['required', 'integer', 'min:1'],
            'usage_limit_per_user' => ['required', 'integer', 'min:1', 'lte:usage_limit_total'],
            'type'                 => ['required', Rule::in(['fixed', 'percentage'])],
            'status'               => ['required', Rule::in(['active', 'inactive','pending'])],
            'start_date'           => ['required', 'date', 'after_or_equal:today'],
            'end_date'             => ['required', 'date', 'after_or_equal:start_date'],

            'apply_on'             => ['required', 'in:product_price,shipping_charge'],
            'bear_by_packly'       => ['nullable', 'boolean'], 

            // Merchant validations
            'merchant_type'  => ['nullable', Rule::in(['1', '2'])],
            'merchant_ids'   => ['required_if:merchant_type,1,2', 'array'],
            'merchant_ids.*' => ['required_if:merchant_type,1,2', 'exists:merchants,id'],

            // Category validations
            'category_type'  => ['nullable', Rule::in(['1', '2'])],
            'category_ids'   => ['required_if:category_type,1,2', 'array'],
            'category_ids.*' => ['required_if:category_type,1,2', 'exists:categories,id'],

            // Brand validations
            'brand_type'  => ['nullable', Rule::in(['1', '2'])],
            'brand_ids'   => ['required_if:brand_type,1,2', 'array'],
            'brand_ids.*' => ['required_if:brand_type,1,2', 'exists:brands,id'],

            // Product validations
            'product_type'  => ['nullable', Rule::in(['1', '2'])],
            'product_ids'   => ['required_if:product_type,1,2', 'array'],
            'product_ids.*' => ['required_if:product_type,1,2', 'exists:products,id'],

            // Variant validations
            'varient'   => ['nullable', 'array'],
            'varient.*' => ['nullable', 'exists:product_variations,id'],
        ];

        if (! $id) {
            $rules['start_date'] = ['required', 'date', 'after_or_equal:today'];
        } else {
            $rules['start_date'] = ['required', 'date'];
        }

        return $rules;
    }

    /**
     * Custom messages for the validation.
     */
    public function messages(): array
    {
        return [
            'max_purchase.gt' => 'Maximum purchase must be greater than minimum purchase',
            'end_date.after'  => 'End date must be after start date',
            'code.unique'     => 'This coupon code has already been taken',
        ];
    }
}
