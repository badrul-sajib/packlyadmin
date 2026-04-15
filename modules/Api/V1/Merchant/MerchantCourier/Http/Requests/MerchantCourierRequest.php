<?php

namespace Modules\Api\V1\Merchant\MerchantCourier\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MerchantCourierRequest extends FormRequest
{
    public function rules(): array
    {
        if ($this->isMethod('post')) {
            return [
                'courier_id' => ['required', 'exists:couriers,id'],
                'base_url'   => 'nullable|url|max:255',
                'api_key'    => 'required|string|max:255',
                'secret_key' => 'required|string|max:255',
                'is_default' => ['required', Rule::in([0, 1])],
                'is_active'  => ['required', Rule::in([0, 1])],
            ];
        }

        if ($this->isMethod('put')) {
            return [
                'base_url'     => 'nullable|url|max:255',
                'api_key'      => 'required|string|max:255',
                'secret_key'   => 'required|string|max:255',
                'auth_token'   => 'required|string|max:255',
                'callback_url' => 'required|url|max:255',
                'is_default'   => ['required', Rule::in([0, 1])],
                'is_active'    => ['required', Rule::in([0, 1])],
            ];
        }

        return [];
    }

    public function authorize(): bool
    {
        return true;
    }
}
