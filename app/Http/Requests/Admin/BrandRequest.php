<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class BrandRequest extends FormRequest
{
    public function rules(): array
    {
        if ($this->isMethod('post')) {
            return [
                'name'  => 'required|string|max:50|unique:brands,name',
                'image' => 'nullable|image|max:2048',
            ];
        }

        if (in_array($this->method(), ['PUT', 'PATCH'])) {
            $brand = $this->route('brand');

            return [
                'name'  => 'required|string|max:50|unique:brands,name,'.$brand->id,
                'image' => 'nullable|image|max:2048',
            ];
        }

        return [];
    }
}
