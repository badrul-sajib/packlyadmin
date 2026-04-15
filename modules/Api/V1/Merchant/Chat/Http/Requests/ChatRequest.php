<?php

namespace Modules\Api\V1\Merchant\Chat\Http\Requests;

use App\Services\ApiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpFoundation\Response;

class ChatRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'receiver_id' => 'required|exists:mysql_external.users,id',
            'message'     => 'required|string|max:1000',
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
