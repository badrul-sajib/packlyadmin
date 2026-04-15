<?php

namespace Modules\Api\V1\Merchant\Customer\Http\Requests;

use App\Enums\CustomerTypes;
use App\Rules\UniqueForMerchant;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class CustomerRequest extends FormRequest
{
    public function rules(): array
    {
        if ($this->isMethod('post')) {
            return [
                'name'             => 'required|string',
                'email'            => ['nullable', 'email', new UniqueForMerchant('customers', 'email')],
                'phone'            => ['required', 'regex:/^01[3-9]\d{8}$/', new UniqueForMerchant('customers', 'phone')],
                'address'          => 'nullable|string|max:255',
                'customer_type_id' => ['required', 'integer', new Enum(CustomerTypes::class)],
                'balance'          => 'nullable|integer',
                'image'            => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            ];
        }

        if (in_array($this->method(), ['PUT', 'PATCH'])) {
            return [
                'name'             => 'required|string',
                'email'            => ['nullable', 'email', new UniqueForMerchant('customers', 'email', 'id', $this->route('customer'))],
                'phone'            => ['required', 'regex:/^01[3-9]\d{8}$/', new UniqueForMerchant('customers', 'phone', 'id', $this->route('customer'))],
                'address'          => 'nullable|string|max:255',
                'customer_type_id' => ['required', 'integer', new Enum(CustomerTypes::class)],
                'balance'          => 'nullable|integer',
                'image'            => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            ];
        }

        return [];
    }

    public function authorize(): bool
    {
        return true;
    }
}
