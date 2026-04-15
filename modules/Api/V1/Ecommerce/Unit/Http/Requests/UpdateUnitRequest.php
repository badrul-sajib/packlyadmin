<?php

namespace Modules\Api\V1\Ecommerce\Unit\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUnitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // 'name'  => 'sometimes|required|string|max:255',
            // 'email' => 'sometimes|required|email|unique:{{snakePluralModuleName}},email,'.$this->route('{{snakeModuleName}}'),
        ];
    }
}
