<?php

namespace Modules\Api\V1\Merchant\Role\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRoleRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name'         => 'sometimes|required|string',
            'description'  => 'sometimes|nullable|string',
            'permissions'  => 'sometimes|array',
            'permissions.*'=> 'exists:permissions,id',
        ];
    }
}
