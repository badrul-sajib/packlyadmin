<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class PrimeViewRequest extends FormRequest
{
    public function rules(): array
    {
        if ($this->isMethod('post')) {
            return [
                'name'           => 'required|unique:prime_views,name',
                'status'         => 'required|in:active,inactive',
                'menu_icon'      => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'background'     => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'show_on_sticky' => 'nullable|boolean',
                'explore_item'   => 'nullable|boolean',
                'start_date'     => 'nullable|date',
                'end_date'       => 'nullable|date',
            ];
        }

        if (in_array($this->method(), ['PUT', 'PATCH'])) {
            $id = $this->route('prime_view');

            return [
                'name'           => 'required|unique:prime_views,name,'.$id,
                'status'         => 'required|in:active,inactive',
                'menu_icon'      => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'background'     => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'show_on_sticky' => 'nullable|boolean',
                'explore_item'   => 'nullable|boolean',
                'start_date'     => 'nullable|date',
                'end_date'       => 'nullable|date',
            ];
        }

        return [];
    }
}
