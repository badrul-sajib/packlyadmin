<?php

namespace Modules\Api\V1\Merchant\Review\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReviewRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'reply_message' => 'required|string',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
