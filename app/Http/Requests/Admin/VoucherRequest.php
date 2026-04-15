<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VoucherRequest extends FormRequest
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
        // Get the voucher ID for updates (this will be passed from the route)
        $id = $this->route('voucher');

        return [
            'name'                 => ['required', 'string', 'max:255', Rule::unique('vouchers', 'name')->ignore($id)],
            'code'                 => ['required', 'string', 'max:50', Rule::unique('vouchers', 'code')->ignore($id)],
            'discount_value'       => ['required', 'numeric', 'min:1'],
            'max_discount_value'   => ['nullable', 'numeric', 'gte:discount_value'],
            'description'          => ['required', 'string', 'max:255'],
            'min_purchase'         => ['required', 'numeric', 'min:0'],
            'max_purchase'         => ['nullable'],
            'usage_limit_total'    => ['required', 'integer', 'min:1'],
            'usage_limit_per_user' => ['required', 'integer', 'min:1', 'lte:usage_limit_total'],

            'status'     => ['required', Rule::in(['active', 'inactive'])],
            'start_date' => ['required', 'date', 'after_or_equal:today'],
            'end_date'   => ['required', 'date', 'after:start_date'],

            // Merchant validations
            'merchant_type'  => ['nullable', Rule::in(['1', '2'])],
            'merchant_ids'   => ['required_if:merchant_type,1,2', 'array'],
            'merchant_ids.*' => ['required_if:merchant_type,1,2', 'exists:merchants,id'],
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
            'code.unique'     => 'This voucher code has already been taken',
        ];
    }
}
