<?php

namespace Modules\Api\V1\Merchant\Merchant\Http\Requests;

use App\Traits\JsonValidation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

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
        $merchant                   = Auth::user()->merchant;
        $nid_front_image            = $merchant->nid_front_image ? 'nullable' : 'required';
        $nid_back_image             = $merchant->nid_back_image ? 'nullable' : 'required';
        $bank_statement_images      = $merchant->bank_statement_images ? 'nullable' : 'required';

        return [
            'nid_front_image'           => "{$nid_front_image}|image|mimes:jpeg,png,jpg,pdf,webp|max:2048",
            'nid_back_image'            => "{$nid_back_image}|image|mimes:jpeg,png,jpg,pdf,webp|max:2048",
            'trade_license_images'      => 'nullable|array',
            'trade_license_images.*'    => 'nullable|image|mimes:jpeg,png,jpg,pdf,webp|max:2048',
            'bank_statement_images'     => "{$bank_statement_images}|array",
            'bank_statement_images.*'   => "{$bank_statement_images}|image|mimes:jpeg,png,jpg,pdf,webp|max:2048",

            'delete_trade_license_ids'    => 'nullable|array',
            'delete_trade_license_ids.*'  => 'nullable|integer|exists:media,id',
            'delete_bank_statement_ids'   => 'nullable|array',
            'delete_bank_statement_ids.*' => 'nullable|integer|exists:media,id',
        ];
    }
}
