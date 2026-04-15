<?php

namespace App\Traits;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;

trait JsonValidation
{
    public function failedValidation(Validator $validator): JsonResponse
    {
        $errors = $validator->errors();

        throw new HttpResponseException(validationError('Validation Error', errors: $errors));
    }
}
