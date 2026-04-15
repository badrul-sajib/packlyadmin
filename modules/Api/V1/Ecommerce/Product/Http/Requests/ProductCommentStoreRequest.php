<?php

namespace Modules\Api\V1\Ecommerce\Product\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ProductCommentStoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'comment' => 'required|string|max:350|unique:product_comments,comment',
        ];
    }

    public function messages(): array
    {
        return [
            'comment.unique' => 'This Question is pending, please wait.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            validationError('Validation Error', $validator->errors())
        );
    }

    public function authorize(): bool
    {
        return true;
    }
}
