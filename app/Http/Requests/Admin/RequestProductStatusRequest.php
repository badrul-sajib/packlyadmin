<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class RequestProductStatusRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'id'            => 'required',
            'status'        => 'required',
            'reject_reason' => 'nullable|string',
        ];
    }
}
