<?php

namespace Modules\Api\V1\Ecommerce\Review\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;

class ReviewRequest extends FormRequest
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
            'order_item_id'   => 'required|unique:reviews,order_item_id',
            'review'          => 'required|string|max:255',
            'rating'          => 'required|integer|min:1|max:5',
            'seller_rating'   => 'required|integer|min:1|max:5',
            'shipping_rating' => 'required|integer|min:1|max:5',
            'images'          => 'nullable|array|max:6',
            'images.*'        => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
        ];
    }

    public function failedValidation(Validator $validator): JsonResponse
    {
        $errors = $validator->errors();

        throw new HttpResponseException(validationError('Validation Error', errors: $errors));
    }
}
