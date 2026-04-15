<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class PrimeViewProductUpdateOrderRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'order'   => 'required|array',
            'order.*' => 'exists:prime_view_product,id',
        ];
    }
}
