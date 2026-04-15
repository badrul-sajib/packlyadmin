<?php

namespace Modules\Api\V1\Merchant\Attribute\Http\Requests;

use App\Rules\UniqueForMerchant;
use App\Services\ApiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpFoundation\Response;

class AttributeRequest extends FormRequest
{
    public function rules(): array
    {
        if ($this->isMethod('post')) {
            return [
                'name' => [
                    'required',
                    'string',
                    'max:30',
                    new UniqueForMerchant('attributes', 'name'),
                ],
                'attribute_values' => 'required',
            ];
        }

        if (in_array($this->method(), ['PUT', 'PATCH'])) {
            return [
                'name' => [
                    'required',
                    'string',
                    'max:30',
                    new UniqueForMerchant('attributes', 'name', 'slug', $this->route('slug')),
                ],
                'attribute_values' => 'nullable',
            ];
        }

        return [];
    }

    public function authorize(): bool
    {
        return true;
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            ApiResponse::validationError('Validation Failed', $validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY)
        );
    }
}
