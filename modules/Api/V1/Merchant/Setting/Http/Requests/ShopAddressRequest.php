<?php

namespace Modules\Api\V1\Merchant\Setting\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ShopAddressRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'map_address'   => 'nullable|string',
            'latitude'      => 'required|string',
            'longitude'     => 'required|string',
        ];
    }
}
