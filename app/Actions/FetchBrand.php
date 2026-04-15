<?php

namespace App\Actions;

use App\Models\Brand\Brand;

class FetchBrand
{
    public function execute($request)
    {
        $search  = $request->input('search', '');
        $status  = $request->input('status', '');
        $perPage = $request->input('per_page', 10);

        return Brand::query()
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->when($status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->select('id', 'name', 'status')
            ->orderBy('id', 'desc')->paginate($perPage)->withQueryString();
    }
}
