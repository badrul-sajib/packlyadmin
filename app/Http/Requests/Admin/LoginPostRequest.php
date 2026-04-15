<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class LoginPostRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'phone_mail' => 'required',
            'password'   => 'required|min:6',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
