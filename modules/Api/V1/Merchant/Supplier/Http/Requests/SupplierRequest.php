<?php

namespace Modules\Api\V1\Merchant\Supplier\Http\Requests;

use App\Rules\UniqueForMerchant;
use Illuminate\Foundation\Http\FormRequest;

class SupplierRequest extends FormRequest
{
    public function rules(): array
    {
        if ($this->isMethod('post')) {
            return [
                'name'           => 'required|string|max:25',
                'contact_person' => 'nullable|string|max:25',
                'phone'          => ['required', 'regex:/^01[3-9]\d{8}$/', new UniqueForMerchant('suppliers', 'phone')],
                'email'          => ['nullable', 'email', new UniqueForMerchant('suppliers', 'email')],
                'address'        => 'required|string|max:500',
                'image'          => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            ];
        }

        if (in_array($this->method(), ['PUT', 'PATCH'])) {
            return [
                'name'           => 'required|string|max:255',
                'contact_person' => 'nullable|string|max:255',
                'phone'          => ['required', 'regex:/^01[3-9]\d{8}$/', new UniqueForMerchant('suppliers', 'phone', 'id', $this->route('supplier'))],
                'email'          => ['nullable', 'email', new UniqueForMerchant('suppliers', 'email', 'id', $this->route('supplier'))],
                'address'        => 'required|string|max:500',
                'balance'        => 'nullable|numeric',
                'image'          => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            ];
        }

        return [];
    }

    public function authorize(): bool
    {
        return true;
    }
}
