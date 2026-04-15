<?php

namespace Modules\Api\V1\Merchant\Product\Http\Requests;

use App\Services\ApiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpFoundation\Response;

class BulkProductCsvRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'products'                           => 'required|array',
            'products.*.name'                    => 'required|string|max:255',
            'products.*.category_name'           => 'required|string',
            'products.*.sub_category_name'       => 'nullable|string',
            'products.*.sub_category_child_name' => 'nullable|string',
            'products.*.brand_name'              => 'nullable|string',
            'products.*.unit_name'               => 'nullable|string',
            'products.*.product_type'            => 'required|string|in:single,variant',
            'products.*.description'             => 'required|string|max:4000',
            'products.*.selling_type'            => 'nullable|string|in:wholesale,retail,both',
            'products.*.purchase_price'          => 'nullable|numeric|min:0',
            'products.*.regular_price'           => 'nullable|numeric|min:0',
            'products.*.discount_price'          => 'nullable|numeric|min:0',
            'products.*.wholesale_price'         => 'nullable|numeric|min:0',
            'products.*.minimum_qty'             => 'nullable|integer|min:0',
            'products.*.opening_stock'           => 'nullable|integer|min:0|max:50000',
            'products.*.weight'                  => 'required|numeric|gt:0',
        ];
    }

    public function messages(): array
    {
        return [
            'products.required'                 => 'The products field is required.',
            'products.array'                    => 'The products field must be an array.',
            'products.*.name.required'          => 'The product name is required.',
            'products.*.category_name.required' => 'The category name is required.',
            'products.*.product_type.required'  => 'The product type is required.',
            'products.*.product_type.in'        => 'The product type must be either single or variant.',
            'products.*.description.required'   => 'The product description is required.',
            'products.*.selling_type.in'        => 'Selling type must be wholesale, retail, or both.',
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
