<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class PrimeViewUpdateOrderRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'order'   => 'required|array',
            'order.*' => 'exists:prime_views,id',
        ];
    }
}
