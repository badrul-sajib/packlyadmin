<?php

namespace Modules\Api\V1\Merchant\Unit\Http\Requests;

use App\Rules\UniqueForMerchant;
use App\Services\ApiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpFoundation\Response;

class UnitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Set to false if you want to restrict access
    }

    public function rules(): array
    {
        if ($this->isMethod('post')) {
            return [
                'name' => [
                    'required',
                    'string',
                    'min:1',
                    'max:30',
                    new UniqueForMerchant('units'),
                ],
            ];
        }

        if (in_array($this->method(), ['PUT', 'PATCH'])) {
            return [
                'name' => [
                    'required',
                    'string',
                    'max:255',
                    new UniqueForMerchant('units', 'name', 'id', $this->route('unit')),
                ],
            ];
        }

        return [];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            ApiResponse::validationError('Validation Failed', $validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY)
        );
    }
}
