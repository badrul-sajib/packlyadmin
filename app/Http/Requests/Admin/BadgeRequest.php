<?php

namespace App\Http\Requests\Admin;

use App\Enums\BadgeType;
use Illuminate\Foundation\Http\FormRequest;

class BadgeRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name'   => 'required|string|max:255|unique:badges,name,'.$this->route('badge'),
            'type'   => 'required|in:'.implode(',', array_map(fn ($case) => $case->value, BadgeType::cases())),
            'status' => 'required|in:0,1',
        ];
    }
}
