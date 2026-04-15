<?php

namespace Modules\Api\V1\Merchant\Return\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReturnRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'merchant_note' => 'required|string',
            'status'        => 'required|integer',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
