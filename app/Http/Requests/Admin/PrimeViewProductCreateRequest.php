<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class PrimeViewProductCreateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'search'        => 'nullable|string|max:255',
            'prime_view_id' => 'nullable|exists:prime_views,id',
        ];
    }
}
