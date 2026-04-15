<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class EPageRequest extends FormRequest
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
            'title'     => 'required|string|max:255|unique:e_pages,title,'.$this->input('id') ?? '',
            'label'     => 'required|integer',
            'status'    => 'required|integer|in:1,2,3,4',
            'serial_no' => 'nullable',
            'content'   => 'required|string',
        ];
    }
}
