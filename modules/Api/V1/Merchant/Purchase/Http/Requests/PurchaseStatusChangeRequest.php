<?php

namespace Modules\Api\V1\Merchant\Purchase\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PurchaseStatusChangeRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'purchase_status_id' => 'required|in:1,2',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
