<?php

namespace Modules\Api\V1\Merchant\Stock\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StockTransferRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'from_warehouse_id' => 'required|integer',
            'to_warehouse_id'   => 'required|integer',
            'reference'         => 'nullable|string',
            'note'              => 'nullable|string',
            'shipping_cost'     => 'nullable|numeric',
            'products'          => 'required|array|min:1',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
