<?php

namespace Modules\Api\V1\Ecommerce\Coupon\Http\Requests;

use App\Traits\JsonValidation;
use Illuminate\Foundation\Http\FormRequest;

class CouponProductEligibilityRequest extends FormRequest
{
    use JsonValidation;

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
        return [
            'product_ids'   => 'required|array',
            'product_ids.*' => 'exists:products,id',
            'coupon_ids'    => 'required|array',
            'coupon_ids.*'  => 'exists:coupons,id',
        ];
    }
}
