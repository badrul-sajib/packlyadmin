<?php

namespace App\Http\Requests\Admin\Campaign;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class CampaignRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        // Image & logo required only on create
        $isRequired = $this->campaign ? 'nullable' : 'required';

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('campaigns', 'name')->ignore($this->campaign?->id),
            ],

            'start_title' => 'required|string|max:255',
            'end_title' => 'required|string|max:255',
            'start_subtitle' => 'required|string|max:255',
            'end_subtitle' => 'required|string|max:255',

            // Campaign dates
            'starts_at' => 'required|date|after_or_equal:now',
            'ends_at' => 'required|date|after_or_equal:starts_at|after_or_equal:now',

            // Vendor request dates
            'vendor_request_start' => 'required|date',
            'vendor_request_end' => 'required|date|after_or_equal:vendor_request_start',

            'visibility_rules' => 'required|string',

            'image' => $isRequired . '|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'logo' => $isRequired . '|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',

            // Tiers
            'tiers' => 'required|array',
            'tiers.*.prime_view_id' => 'required|distinct|exists:prime_views,id',
            'tiers.*.discount_amount' => 'required|numeric',
            'tiers.*.discount_type' => 'required|in:1,2',
            'tiers.*.rules' => 'required|string',
        ];
    }

    /**
     * Custom validation logic
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            $startsAt = $this->input('starts_at');
            $vendorStart = $this->input('vendor_request_start');
            $vendorEnd = $this->input('vendor_request_end');

            // Vendor request must NOT exceed campaign start date
            if ($startsAt && $vendorStart && $vendorStart > $startsAt) {
                $validator->errors()->add(
                    'vendor_request_start',
                    'Vendor request start date must be before or equal to campaign start date.'
                );
            }

            if ($startsAt && $vendorEnd && $vendorEnd > $startsAt) {
                $validator->errors()->add(
                    'vendor_request_end',
                    'Vendor request end date must be before or equal to campaign start date.'
                );
            }
        });
    }

    /**
     * Custom error messages
     */
    public function messages(): array
    {
        return [
            'starts_at.after_or_equal' => 'Campaign start date must be today or later.',
            'ends_at.after_or_equal' => 'Campaign end date must be today or after the start date.',

            'vendor_request_end.after_or_equal' => 'Vendor request end date must be after or equal to vendor request start date.',

            'tiers.*.prime_view_id.exists' => 'The selected prime view is invalid.',
            'tiers.*.discount_type.in' => 'Discount type must be either 1 or 2.',
            'tiers.*.discount_amount.numeric' => 'Discount amount must be numeric.',
            'tiers.*.rules.required' => 'Tier rules are required.',

            'visibility_rules.required' => 'Visibility rules are required.',
            'vendor_request_start.required' => 'Vendor request start date is required.',
            'vendor_request_end.required' => 'Vendor request end date is required.',
        ];
    }
}
