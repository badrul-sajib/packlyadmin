<?php

namespace Modules\Api\V1\Merchant\Product\Http\Requests;

use App\Services\ApiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpFoundation\Response;

class ProductRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'min:3',
                'max:255',
                function ($attribute, $value, $fail) {
                    // Allow Bengali + English letters, digits, spaces, hyphens, underscores, ampersands
                    if (! preg_match('/^[\p{Bengali}A-Za-z0-9০-৯\s\-\_&\%\(\)\{\}]+$/u', $value)) {
                        return $fail("The {$attribute} contains invalid characters.");
                    }

                    // Reject if only symbols or spaces
                    if (preg_match('/^[\-\_\.\/\+\*\#\@\!\s]+$/u', $value)) {
                        return $fail("The {$attribute} cannot consist only of symbols or spaces.");
                    }

                    // Reject if only English numbers
                    if (preg_match('/^[0-9]+$/u', $value)) {
                        return $fail("The {$attribute} cannot consist only of numbers.");
                    }

                    // Reject if only Bangla numbers (০-৯)
                    if (preg_match('/^[০-৯]+$/u', $value)) {
                        return $fail("The {$attribute} cannot consist only of Bangla numbers.");
                    }
                },
            ],

            'category_id'           => 'required|integer',
            'sub_category_id'       => 'nullable|integer',
            'sub_category_child_id' => 'nullable|integer',
            'brand_id'              => 'nullable|integer',
            'unit_id'               => 'nullable|integer',
            'description'           => 'required|string',
            'specification'         => 'nullable|string',
            'selling_type_id'       => 'nullable|integer|in:1,2,3',
            'images'                => 'nullable|array',
            'images.*'              => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'thumbnail'             => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'warranty_note'         => 'nullable|string|max:255',
            'purchase_price'        => $this->product_type_id == 1 ? 'required|numeric' : 'nullable',
            'regular_price'         => $this->product_type_id == 1 ? 'required|numeric|min:1' : 'nullable',
            'discount_price'        => $this->product_type_id == 1 ? 'required|numeric|min:1|lte:regular_price' : 'nullable',
            'product_type_id'       => [
                'required',
                'integer',
                'in:1,2',
            ],
            'stock_qty' => [
                'nullable',
                'integer',
                'min:0',
            ],
            'weight' => 'required|numeric|gt:0',
            'wholesale_price' => 'nullable|numeric|min:0',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }

    public function messages(): array
    {
        return [
            'warranty_note.max'    => 'Warranty Policy must not exceed 255 characters.',
            'stock_qty.integer'    => 'Stock quantity must be a valid integer.',
            'stock_qty.min'        => 'Stock quantity cannot be less than 0.',
            'discount_price.lte'   => 'Discount price cannot be greater than regular price.',
            'category_id.required' => 'The category field is required.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            ApiResponse::validationError('Validation Failed', $validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY)
        );
    }
}
