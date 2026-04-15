<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class CategoryCreateRequestRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'type' => 'required|in:1,2,3',
        ];
    }
}
