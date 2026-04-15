<?php

namespace App\Http\Requests\Admin;

use DB;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class PrimeViewProductRequest extends FormRequest
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
            'prime_view_id' => 'required|exists:prime_views,id',
            'products'      => 'required|array',
            'products.*'    => 'exists:products,id',
        ];
    }

    public function messages(): array
    {
        return [
            'prime_view_id.required' => 'Prime view id is required',
            'prime_view_id.exists'   => 'Prime view id does not exist',
            'products.required'      => 'Products are required',
            'products.array'         => 'Products must be an array',
            'products.*.exists'      => 'Product id does not exist',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            $primeViewId = $this->input('prime_view_id');
            $products    = $this->input('products', []);

            // Fetch existing product IDs for the given prime view
            $existingProducts = \DB::table('prime_view_product')
                ->where('prime_view_id', $primeViewId)
                ->pluck('product_id')
                ->toArray();

            // Check for duplicates and add custom error messages
            foreach ($products as $productId) {
                if (in_array($productId, $existingProducts)) {
                    $productName = DB::table('products')->where('id', $productId)->value('name');
                    $validator->errors()->add("products.{$productId}", "Product '{$productName}' already exists in prime view.");
                }
            }
        });
    }
}
