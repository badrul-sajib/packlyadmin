<?php

namespace Modules\Api\V1\Merchant\Auth\Http\Requests;

use App\Rules\UniqueMerchantEmail;
use App\Rules\UniqueMerchantPhone;
use App\Services\ApiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpFoundation\Response;

class MerchantRegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Set to false if you want to restrict access
    }

    public function rules(): array
    {
        return [
            'name'     => 'required|string|max:255',
            'phone'    => ['required', 'regex:/^01[3-9]\d{8}$/', new UniqueMerchantPhone],
            'email'    => ['nullable', 'email', 'max:255', new UniqueMerchantEmail],
            'password' => 'required|string|min:6',
            'otp_type' => 'required|in:email,phone',

            // Merchant fields
            'shop_address' => 'nullable|string|max:500',
            'shop_name'    => 'nullable|string|max:255',
            'shop_url'     => 'nullable|url|unique:merchants,shop_url',
        ];
    }

    public function messages(): array
    {
        return [
            'phone.regex'  => 'Invalid phone number format.',
            'phone.unique' => 'Duplicate phone number.',
            'email.unique' => 'Duplicate email address.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            ApiResponse::validationError('Validation Failed', $validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY)
        );
    }
}
