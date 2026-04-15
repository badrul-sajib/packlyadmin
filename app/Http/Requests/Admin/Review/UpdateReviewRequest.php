<?php

namespace App\Http\Requests\Admin\Review;

use Illuminate\Foundation\Http\FormRequest;

class UpdateReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'review'           => ['required', 'string'],
            'rating'           => ['required', 'numeric', 'min:1', 'max:5'],
            'seller_rating'    => ['nullable', 'numeric', 'min:1', 'max:5'],
            'shipping_rating'  => ['nullable', 'numeric', 'min:1', 'max:5'],
        ];
    }
}
