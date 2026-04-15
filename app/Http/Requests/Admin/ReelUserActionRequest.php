<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;

class ReelUserActionRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'reel_id'     => 'required|integer|exists:reels,id',
            'action_type' => 'required|in:like,unlike,message,block,unblock,follow,unfollow',
            'message'     => 'required_if:action_type,message|string|nullable',
        ];
    }

    public function failedValidation(Validator $validator): JsonResponse
    {
        $errors = $validator->errors();

        throw new HttpResponseException(validationError('Validation Error', errors: $errors));
    }
}
