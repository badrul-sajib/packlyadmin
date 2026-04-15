<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class SellWithUsRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'data'            => 'nullable|array',
            'data.*.name'     => 'required|string',
            'data.*.type'     => 'required|in:text,textarea,file,password',
            'data.*.value'    => 'nullable',
            'items'           => 'nullable|array',
            'items.*'         => 'array',
            'items.*.*.name'  => 'required|string',
            'items.*.*.type'  => 'required|in:text,textarea,file,password',
            'items.*.*.value' => 'nullable',
        ];
    }
}
