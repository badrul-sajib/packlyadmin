<?php

namespace Modules\Api\V1\Merchant\Auth\Http\Requests;

use App\Rules\UniqueMerchantPhone;
use App\Services\ApiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpFoundation\Response;

class PhoneValidationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {

        if ($this->has('new_registration') && $this->boolean('new_registration')) {
            return [
                'phone' => ['required', 'regex:/^01[3-9]\d{8}$/', new UniqueMerchantPhone],
            ];
        }

        return [
            'phone' => 'required|exists:mysql_external.users,phone',
        ];
    }

    public function messages(): array
    {
        return [
            'phone.unique' => 'The phone number is already registered.',
            'phone.exists' => 'The phone number is not registered with us.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            ApiResponse::validationError('Validation Failed', $validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY)
        );
    }
}
