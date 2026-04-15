<?php

namespace Modules\Api\V1\Merchant\Auth\Http\Requests;

use App\Rules\UniqueMerchantEmail;
use App\Rules\UniqueMerchantPhone;
use App\Services\ApiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpFoundation\Response;

class RegisterWithKeyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Set to false if you want to restrict access
    }

    public function rules(): array
    {
        return [
            'reg_key'      => 'required',
            'name'         => 'required|string|max:255',
            'phone'        => ['required', 'regex:/^01[3-9]\d{8}$/', new UniqueMerchantPhone],
            'email'        => ['nullable', 'email', 'max:255', new UniqueMerchantEmail],
            'password'     => 'required|string|min:8',
            'shop_name'    => 'required|string|max:255|unique:merchants,shop_name',
            'shop_address' => 'required|string|max:500',
            'shop_url'     => 'required|url|unique:merchants,shop_url',
            'map_address'  => 'nullable|string',
            'latitude'     => 'nullable|string',
            'longitude'    => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'phone.exists' => 'The mobile number is not registered with us.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            ApiResponse::validationError('Validation Failed', $validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY)
        );
    }
}
