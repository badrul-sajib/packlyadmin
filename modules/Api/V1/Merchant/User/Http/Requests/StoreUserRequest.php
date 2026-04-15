<?php

namespace Modules\Api\V1\Merchant\User\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\UniqueMerchantEmail;
use App\Rules\UniqueMerchantPhone;

class StoreUserRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name'     => 'required|string|max:255',
            'phone' => ['required','regex:/^01[3-9]\d{8}$/', new UniqueMerchantPhone()],
            'email' => ['nullable', 'email', 'max:255', new UniqueMerchantEmail()],
            'password' => 'required|string|min:8|confirmed',
            'roles'    => 'sometimes|array',
            'roles.*'  => 'exists:roles,id',
        ];
    }
}