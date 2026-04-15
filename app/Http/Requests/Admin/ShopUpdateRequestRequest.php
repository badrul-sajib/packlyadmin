<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ShopUpdateRequestRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'status' => 'required|in:approved,rejected',
        ];
    }
}
