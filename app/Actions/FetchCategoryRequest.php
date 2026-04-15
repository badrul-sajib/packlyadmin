<?php

namespace App\Actions;

use App\Models\Category\CategoryCreateRequest;

class FetchCategoryRequest
{
    public function execute($request)
    {
        $perPage = $request->perPage ?? 10;
        $status  = $request->status  ?? null;
        $page    = $request->page    ?? 1;

        return CategoryCreateRequest::with('merchant', 'merchant.userRelation')
            ->when($status, function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->latest()
            ->select('id', 'data', 'status', 'merchant_id', 'created_at')
            ->paginate($perPage, ['*'], 'page', $page)
            ->withQueryString();
    }
}
