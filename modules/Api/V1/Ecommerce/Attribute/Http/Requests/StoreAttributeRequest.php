<?php

namespace Modules\Api\V1\Ecommerce\Attribute\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAttributeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // 'name' => 'required|string|max:255',
            // 'email' => 'required|email|unique:users,email',
        ];
    }
}
