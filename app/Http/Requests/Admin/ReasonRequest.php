<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ReasonRequest extends FormRequest
{
    public function rules(): array
    {
        if ($this->isMethod('post')) {
            return [
                'type'   => 'required|in:cancel,return,exchange,refund',
                'name'   => 'required|unique:reasons,name',
                'status' => 'required',
            ];
        }

        if (in_array($this->method(), ['PUT', 'PATCH'])) {
            $id = $this->route('reason');

            return [
                'type'   => 'required|in:cancel,return,exchange,refund',
                'name'   => 'required|unique:reasons,name,'.$id,
                'status' => 'required',
            ];
        }

        return [];
    }
}
