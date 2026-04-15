<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class BulkProductStatusRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'product_ids'       => 'required|array',
            'product_ids.*'     => 'required|exists:shop_products,id',
            'status'            => 'required',
            'reject_reason'     => 'nullable|string',
        ];
    }
}
