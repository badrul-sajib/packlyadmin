<?php

namespace Modules\Api\V1\Merchant\Sale\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SellProductStatusChangeRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'sell_status_id' => 'required|in:1,2',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
