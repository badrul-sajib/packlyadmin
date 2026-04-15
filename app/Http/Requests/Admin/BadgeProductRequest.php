<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;

class BadgeProductRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'badge_id'      => 'required|exists:badges,id',
            'product_ids'   => 'nullable|array',
            'product_ids.*' => [
                'nullable',
                'exists:shop_products,product_id',
                function ($attribute, $value, $fail) {
                    $exists = DB::table('badge_products')
                        ->where('badge_id', $this->input('badge_id'))
                        ->where('product_id', $value)
                        ->exists();

                    if (! $exists) {
                        // If the product doesn't exist for the given badge_id, enforce uniqueness
                        $duplicate = DB::table('badge_products')
                            ->where('product_id', $value)
                            ->exists();

                        if ($duplicate) {
                            $fail("The product ID $value is already assigned to another badge.");
                        }
                    }
                },
            ],
            'varient'   => 'nullable|array',
            'varient.*' => 'nullable|exists:product_variations,id',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
