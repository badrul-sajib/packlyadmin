<?php

namespace Modules\Api\V1\Merchant\Coupon\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
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
        $coupon = $this->route('coupon');

        return [
            'name'           => ['required', 'string', 'max:255', Rule::unique('coupons', 'name')->ignore($coupon->id ?? null)],
            'code'           => ['required', 'string', 'max:10', Rule::unique('coupons', 'code')->ignore($coupon->id ?? null)],
            'discount_value' => ['required', 'numeric', 'min:1'],

            'type' => ['required', Rule::in(['fixed', 'percentage'])],

            'max_discount_value' => [
                'required_unless:type,fixed',
            ],

            'description'          => ['required', 'string', 'max:255'],
            'min_purchase'         => ['required', 'numeric', 'min:0'],
            'max_purchase'         => ['nullable'],
            'usage_limit_total'    => ['required', 'integer', 'min:1'],
            'usage_limit_per_user' => ['required', 'integer', 'min:1', 'lte:usage_limit_total'],

            'start_date' => ['required', 'date', 'after_or_equal:today'],
            'end_date'   => ['required', 'date', 'after:start_date'],

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

    public function failedValidation(Validator $validator): JsonResponse
    {
        $errors = $validator->errors();

        throw new HttpResponseException(validationError('Validation Error', errors: $errors));
    }
}
