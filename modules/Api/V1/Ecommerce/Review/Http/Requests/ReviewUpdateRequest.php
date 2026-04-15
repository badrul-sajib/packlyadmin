<?php

namespace Modules\Api\V1\Ecommerce\Review\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ReviewUpdateRequest extends FormRequest
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
            'review'             => 'required|string|max:255',
            'rating'             => 'required|integer|min:1|max:5',
            'seller_rating'      => 'required|integer|min:1|max:5',
            'shipping_rating'    => 'required|integer|min:1|max:5',
            'images'             => 'nullable|array|max:6',
            'images.*'           => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'delete_image_ids'   => 'nullable|array',
            'delete_image_ids.*' => 'nullable|integer|exists:media,id',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'message' => 'The given data was invalid.',
            'errors'  => $validator->errors(),
        ], 422));
    }
}
