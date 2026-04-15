<?php

namespace Modules\Api\V1\Merchant\CategoryRequest\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CategoryRequestRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'note'       => 'nullable|string|max:500',
            'categories' => 'required|json',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
