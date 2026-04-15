<?php

namespace App\Actions;

use App\Models\User\User;

class CustomerList
{
    public function __construct() {}

    public function execute($request)
    {
        $search   = $request->input('search', '');
        $perPage  = $request->input('perPage', 10);
        $page     = $request->input('page', 1);

        return User::customer() // customer()
            ->with([
                'addresses.location.parent.parent',
            ])
            ->when($search, function ($query) use ($search) {
                $query->whereAny(['phone', 'email'], 'like', "%{$search}%");
            })
            ->orderBy('id', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);
    }
}
