<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MerchantRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        $id = $this->route('merchant') ?? null;

        return [
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:mysql_external.users,email,'.$id,
            'phone' => [
                'required',
                'regex:/^01[3-9]\d{8}$/',
                'string',
                'max:15',
                Rule::unique('mysql_external.users', 'phone')->ignore($id),
            ],

            'shop_address' => 'required|string|max:255',
            'shop_name'    => 'required|string|max:255',
            'shop_url'     => 'required|url',
            'shop_status'  => 'required|in:1,2',
            'password'     => 'required|string|min:8|max:50',
        ];
    }
}
