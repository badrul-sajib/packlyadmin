<?php

namespace App\Http\Requests\Admin\Merchant;

use Illuminate\Foundation\Http\FormRequest;

class MerchantConfigurationRequest extends FormRequest
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
        return [
            // Base configuration
            'min_amount'                => 'required|numeric|max:1000000',
            'per_day_request'           => 'required|numeric|min:1|max:1000',
            'payout_charge'             => 'required|numeric|max:250',
            'maximum_product_request'   => 'required|numeric|max:1000',
            'commission_rate'           => 'required|numeric|max:1000',
            'payout_request_date'       => 'required|numeric|min:1|max:100',
            'id_delivery_fee'           => 'required|numeric|min:0|max:50000',
            'od_delivery_fee'           => 'required|numeric|min:0|max:50000',
            'ed_delivery_fee'           => 'required|numeric|min:0|max:50000',
            
            /*
            |--------------------------------------------------------------------------
            | Product-level commission
            |--------------------------------------------------------------------------
            */
            'product_ids'                 => 'nullable|array|required_with:product_commission_rate',
            'product_ids.*'               => 'required_with:product_ids|integer|exists:products,id',

            'product_commission_rate'     => 'nullable|array|required_with:product_ids',
            'product_commission_rate.*'   => 'required_with:product_commission_rate|numeric|min:0|max:100',
        ];
    }

    public function messages(): array
    {
        return [
            // Base config
            'min_amount.required' => 'Minimum amount is required.',
            'min_amount.numeric'  => 'Minimum amount must be a valid number.',
            'min_amount.max'      => 'Minimum amount cannot exceed 1,000,000.',

            'per_day_request.required' => 'Daily request limit is required.',
            'per_day_request.integer'  => 'Daily request limit must be an integer.',
            'per_day_request.max'      => 'Daily request limit cannot exceed 1000.',

            'payout_charge.required' => 'Payout charge is required.',
            'payout_charge.numeric'  => 'Payout charge must be numeric.',
            'payout_charge.max'      => 'Payout charge cannot be more than 250.',

            'maximum_product_request.required' => 'Maximum product request is required.',
            'maximum_product_request.integer'  => 'Maximum product request must be an integer.',
            'maximum_product_request.max'      => 'Maximum product request cannot exceed 1000.',

            // Product-level
            'product_ids.required_with'               => 'Product IDs are required when product commission rates are provided.',
            'product_ids.*.required_with'             => 'Each product ID is required.',
            'product_ids.*.integer'                   => 'Each product ID must be an integer.',
            'product_ids.*.exists'                    => 'Some product IDs are invalid.',

            'product_commission_rate.required_with'   => 'Product commission rates are required when product IDs are provided.',
            'product_commission_rate.*.required_with' => 'Each product commission rate is required.',
            'product_commission_rate.*.numeric'       => 'Commission rate must be numeric.',
            'product_commission_rate.*.min'           => 'Commission rate cannot be negative.',
            'product_commission_rate.*.max'           => 'Commission rate cannot exceed 100%.',
        ];
    }

}
