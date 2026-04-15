<?php

namespace App\Modules\RolePermission\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RoleStoreRequest extends FormRequest
{
    public function rules(): array
    {
        if ($this->isMethod('post')) {
            return [
                'name'          => 'required|unique:roles,name|max:50',
                'guard_name'    => 'required|in:web,admin',
                'permissions'   => 'required|array',
                'permissions.*' => 'required|exists:permissions,name',
            ];
        }

        if (in_array($this->method(), ['PUT', 'PATCH'])) {
            return [
                'name'          => 'required|max:50',
                'guard_name'    => 'required|in:web,admin',
                'permissions'   => 'required|array',
                'permissions.*' => 'required|exists:permissions,name',
            ];
        }

        return [];
    }

    public function authorize(): bool
    {
        return true;
    }
}
