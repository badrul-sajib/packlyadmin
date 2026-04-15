<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class GuestAdminSendOtpRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'phone' => 'required|exists:mysql_external.users,phone',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
