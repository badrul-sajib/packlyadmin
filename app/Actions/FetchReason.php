<?php

namespace App\Actions;

use App\Models\Order\Reason;

class FetchReason
{
    public function execute($request)
    {
        $type   = $request->input('type', '');
        $search = $request->input('search', '');
        $status = $request->input('status', '');

        return Reason::query()
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->when($type, function ($query, $type) {
                $query->where('type', $type);
            })
            ->when($status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->select('id', 'name', 'type', 'status')
            ->orderBy('id', 'desc')->get();
    }
}
