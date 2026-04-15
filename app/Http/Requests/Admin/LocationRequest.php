<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;

class LocationRequest extends FormRequest
{
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
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'parent_id' => 'nullable|exists:locations,id',
            'name'      => 'required|string|max:255',
        ];
    }

    public function failedValidation(Validator $validator): JsonResponse
    {
        $errors = $validator->errors();

        throw new HttpResponseException(validationError('Validation Error', errors: $errors));
    }
}
