<?php

namespace Modules\Api\V1\Merchant\Product\Http\Requests;

use App\Services\ApiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpFoundation\Response;

class ProductCsvRowRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name'                    => 'required|string|max:255',
            'category_name'           => 'required',
            'sub_category_child_name' => 'nullable',
            'product_type'            => 'required|string|in:single,variant',
            'description'             => 'required',
            'selling_type'            => 'nullable|string|in:wholesale,retail,both',
            'weight'                  => 'required|numeric|gt:0',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'          => 'The product name is required.',
            'category_name.required' => 'The category name is required.',
            'product_type.required'  => 'The product type is required.',
            'product_type.in'        => 'The product type must be either single or variant.',
            'description.required'   => 'The product description is required.',
            'selling_type.in'        => 'The selling type must be either wholesale, retail, or both.',
        ];
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
