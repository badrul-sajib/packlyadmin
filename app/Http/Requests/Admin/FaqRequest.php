<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class FaqRequest extends FormRequest
{
    public function rules(): array
    {
        if ($this->isMethod('post')) {
            return [
                'question'  => 'required|string|max:500|unique:faqs,question',
                'answer'    => 'required|max:5000',
            ];
        }

        if (in_array($this->method(), ['PUT', 'PATCH'])) {
            $faq = $this->route('faq');
            $id = $faq instanceof \App\Models\Page\Faq ? $faq->id : $faq;

            return [
                'question' => 'required|string|max:500|unique:faqs,question,'.$id,
                'answer'   => 'required|max:5000',
            ];
        }

        return [];
    }
}
