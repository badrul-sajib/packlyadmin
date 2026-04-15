<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class CategoryImportRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'file' => 'required|mimes:xls,xlsx',
        ];
    }
}
