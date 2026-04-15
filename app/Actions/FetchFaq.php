<?php

namespace App\Actions;

use App\Models\Page\Faq;

class FetchFaq
{
    public function execute($request)
    {
        $search  = $request->input('search', '');
        $status  = $request->input('status', null); // example value 0/1
        $perPage = $request->input('perPage', 10);

        return Faq::query()
            ->when($search, function ($query, $search) {
                $query->whereAny(['question', 'answer'], 'like', "%{$search}%");
            })
            ->when(!is_null($status), function ($query) use ($status) {

                $query->where('status', $status);
            })
            ->select('id', 'question', 'answer', 'status')
            ->orderBy('id', 'desc')
            ->paginate($perPage)
            ->withQueryString();
    }
}
