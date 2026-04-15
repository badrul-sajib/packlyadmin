<?php

namespace App\Http\Requests\Admin\Product;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'min:3',
                'max:255',
                function ($attribute, $value, $fail) {
                    if (! preg_match('/^[\p{Bengali}A-Za-z0-9ঀ-৿_\s\-\_&]+$/u', $value)) {
                        return $fail("The {$attribute} contains invalid characters.");
                    }

                    if (preg_match('/^[\-\_\.\/\+\*\#\@\!\s]+$/u', $value)) {
                        return $fail("The {$attribute} cannot consist only of symbols or spaces.");
                    }

                    if (preg_match('/^[0-9]+$/u', $value)) {
                        return $fail("The {$attribute} cannot consist only of numbers.");
                    }

                    if (preg_match('/^[০-৯]+$/u', $value)) {
                        return $fail("The {$attribute} cannot consist only of Bangla numbers.");
                    }
                },
            ],
            'sku'                      => 'nullable|string|max:255',
            'category_id'              => 'required|integer|exists:categories,id',
            'sub_category_id'          => 'nullable|integer|exists:sub_categories,id',
            'sub_category_child_id'    => 'nullable|integer|exists:sub_category_children,id',
            'brand_id'                 => 'nullable|integer|exists:brands,id',
            'unit_id'                  => 'nullable|integer|exists:units,id',
            'weight'                   => 'nullable|numeric|min:0',
            'has_warranty'             => 'required|in:0,1',
            'warranty_note'            => 'nullable|string|max:255',
            'replace_status'           => 'nullable|in:1',
            'replace_recurring_period' => 'nullable|integer|min:0',
            'replace_recurring_type'   => 'nullable|in:1,2,3',
            'service_status'           => 'nullable|in:1',
            'service_recurring_period' => 'nullable|integer|min:0',
            'service_recurring_type'   => 'nullable|in:1,2,3',
            'description'              => 'nullable|string',
            'specification'            => 'nullable|string',
            'thumbnail'                => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'remove_thumbnail'         => 'nullable|in:0,1',
            'images.*'                 => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'remove_images'            => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'warranty_note.max' => 'Warranty Policy must not exceed 255 characters.',
        ];
    }
}

