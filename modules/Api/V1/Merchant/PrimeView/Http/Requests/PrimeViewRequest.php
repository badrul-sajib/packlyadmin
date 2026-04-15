<?php

namespace Modules\Api\V1\Merchant\PrimeView\Http\Requests;

use App\Services\ApiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpFoundation\Response;

class PrimeViewRequest extends FormRequest
{
    public function rules(): array
    {
        if ($this->isMethod('post')) {
            return [
                'prime_view_id' => 'required|integer',
                'product_id'    => 'required|integer',
                'status'        => 'required|string|in:pending,inactive',
            ];
        }

        if (in_array($this->method(), ['PUT', 'PATCH'])) {
            return [
                'status' => 'required|string|in:inactive',
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

    public function authorize(): bool
    {
        return true;
    }
}
