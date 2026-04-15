<?php

namespace Modules\Api\V1\Ecommerce\Account\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AccountDeleteFormRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'password' => 'nullable|string|current_password',
            'reason'   => 'nullable|string|max:500',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
