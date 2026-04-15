<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class PasswordUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'old_password' => 'required|current_password',
            'password'     => 'required|min:8|confirmed',
        ];
    }
}
