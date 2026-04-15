<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CategoryRequest extends FormRequest
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
        $id = $this->route('category');

        $rules = [
            'type'       => 'required',
            'status'     => 'required|in:0,1',
        ];
        
        if ($this->type == '1') {
            $rules['name'] = 'required|string|max:255|unique:categories,name,'.$id;
        }
        if ($this->type == '2') {
            $rules['name']        = 'required|string|max:255|unique:sub_categories,name,'.$id;
            $rules['category_id'] = 'required|exists:categories,id';
        }
        if ($this->type == '3') {
            $rules['name']            = 'required|string|max:255|unique:sub_category_children,name,'.$id;
            $rules['sub_category_id'] = 'required|exists:sub_categories,id';
        }
        $rules['image'] = 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048';

        return $rules;
    }
}
