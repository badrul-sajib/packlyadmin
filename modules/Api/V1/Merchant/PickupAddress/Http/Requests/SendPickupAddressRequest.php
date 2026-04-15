<?php

namespace Modules\Api\V1\Merchant\PickupAddress\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SendPickupAddressRequest extends FormRequest
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
            'note' => 'nullable|string|max:500',
            'estim_qty'         => 'nullable|numeric|min:0',
        ];
    }
}
