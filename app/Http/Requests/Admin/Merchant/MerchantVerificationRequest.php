<?php

namespace App\Http\Requests\Admin\Merchant;

use App\Enums\MerchantVerificationStatus;
use App\Traits\JsonValidation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MerchantVerificationRequest extends FormRequest
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
            'is_verified'                => ['required', Rule::in(MerchantVerificationStatus::values())],
            'nid_front_image'            => 'nullable|image|mimes:jpeg,png,jpg,pdf,webp|max:2048',
            'nid_back_image'             => 'nullable|image|mimes:jpeg,png,jpg,pdf,webp|max:2048',
            'trade_license_images'       => 'nullable|array',
            'trade_license_images.*'     => 'nullable|image|mimes:jpeg,png,jpg,pdf,webp|max:2048',
            'bank_statement_images'      => 'nullable|array',
            'bank_statement_images.*'    => 'nullable|image|mimes:jpeg,png,jpg,pdf,webp|max:2048',
        ];
    }
}
