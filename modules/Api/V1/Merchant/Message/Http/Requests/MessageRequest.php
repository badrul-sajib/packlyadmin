<?php

namespace Modules\Api\V1\Merchant\Message\Http\Requests;

use App\Services\ApiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpFoundation\Response;

class MessageRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'phone' => [
                'required',
                'regex:/^(?:\+88)?01[3-9]\d{8}$/',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'message' => 'required|string|max:255',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            ApiResponse::validationError('Validation Failed', $validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY)
        );
    }

    public function authorize(): bool
    {
        return true;
    }
}
