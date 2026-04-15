<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class NewPasswordStoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'otp'              => 'required|exists:otps,otp',
            'new_password'     => 'required|min:8',
            'confirm_password' => 'required|same:new_password',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
