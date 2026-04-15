<?php

namespace Modules\Api\V1\Merchant\Warehouse\Http\Requests;

use App\Rules\UniqueForMerchant;
use Illuminate\Foundation\Http\FormRequest;

class WarehouseRequest extends FormRequest
{
    public function rules(): array
    {
        if ($this->isMethod('post')) {
            return [
                'name'    => ['required', 'string', 'max:255', new UniqueForMerchant('warehouses', 'name')],
                'phone'   => ['required', 'regex:/^01[3-9]\d{8}$/', new UniqueForMerchant('warehouses', 'phone')],
                'address' => 'required|string|max:500',
            ];
        }

        if (in_array($this->method(), ['PUT', 'PATCH'])) {
            return [
                'name'    => ['required', 'string', 'max:255', new UniqueForMerchant('warehouses', 'name', 'id', $this->route('warehouse'))],
                'phone'   => ['required', 'regex:/^01[3-9]\d{8}$/', new UniqueForMerchant('warehouses', 'phone', 'id', $this->route('warehouse'))],
                'address' => 'required|string|max:500',
            ];
        }

        return [];
    }

    public function authorize(): bool
    {
        return true;
    }
}
