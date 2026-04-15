<?php

namespace Modules\Api\V1\Merchant\User\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\UniqueMerchantEmail;
use App\Rules\UniqueMerchantPhone;

class UpdateUserRequest extends FormRequest
{
    public function rules(): array
    {

        $user = (int) $this->route('user');

        return [
            'name'     => 'sometimes|required|string|max:255',
            'phone'    => ['sometimes', 'required', 'regex:/^01[3-9]\d{8}$/', new UniqueMerchantPhone($user)],
            'email'    => ['sometimes', 'nullable', 'email', 'max:255', new UniqueMerchantEmail($user)],
            'password' => 'sometimes|nullable|string|min:8|confirmed',
            'roles'    => 'sometimes|array',
            'roles.*'  => 'exists:roles,id',
        ];
    }
}
