<?php

namespace Modules\Api\V1\Merchant\Issue\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MerchantIssueRequest extends FormRequest
{
    public function rules(): array
    {
        $isUpdate = in_array($this->method(), ['PUT', 'PATCH']);

        return [
            'merchant_issue_type_id' => [$isUpdate ? 'sometimes' : 'required', 'integer', 'exists:merchant_issue_types,id'],
            'message'                => [$isUpdate ? 'sometimes' : 'required', 'string'],
            'attachments'            => ['sometimes', 'array', 'max:5'],
            'attachments.*'          => 'file|mimes:jpg,jpeg,png,webp,gif,svg,mp4,mov,avi,webm|max:12288',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
