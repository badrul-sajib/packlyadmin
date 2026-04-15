<?php

namespace Modules\Api\V1\Merchant\Account\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class JournalRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'date'                  => 'required|date',
            'journal_no'            => 'required|numeric|unique:journals,journal_no',
            'referrence'            => 'nullable|string',
            'note'                  => 'nullable|string',
            'merchant_id'           => 'required|integer',
            'details'               => 'required|array',
            'details.*.type'        => 'required|in:debit,credit',
            'details.*.account_id'  => 'required|integer',
            'details.*.description' => 'nullable|string',
            'details.*.contact_id'  => 'required|integer',
            'details.*.amount'      => 'required|numeric|min:0',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
