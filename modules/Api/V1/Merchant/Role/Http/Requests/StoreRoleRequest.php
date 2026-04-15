<?php

namespace Modules\Api\V1\Merchant\Role\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRoleRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name'         => 'required|string',
            'description'  => 'nullable|string',
            'permissions'  => 'required|array|min:1',
            'permissions.*' => 'exists:permissions,id',
        ];
    }

    public function messages(): array
    {
        return [
            'permissions.required' => 'No permission Selected',
            'permissions.min' => 'No permission Selected',
            'permissions.array' => 'No permission Selected',
            'permissions.*.exists' => 'Selected permission is invalid',
        ];
    }
}
