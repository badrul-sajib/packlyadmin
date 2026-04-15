<?php

namespace App\Services\Badge;

use App\Models\Product\Badge;

class BadgeServices
{
    public function getAll($request)
    {
        $perPage = $request->perPage ? $request->perPage : 10;
        $page    = $request->input('page', 1);
        $search  = $request->input('search', '');

        return Badge::query()
            ->with('badge_products')
            ->when($search, function ($query) use ($search) {
                return $query->where('name', 'like', '%'.$search.'%');
            })
            ->latest()
            ->paginate($perPage, ['*'], 'page', $page);
    }

    public function create($data): Badge
    {
        return Badge::create($data);
    }

    public function update($data, $id): Badge
    {
        $badge = Badge::find($id);
        $badge->update($data);

        return $badge;
    }

    public function getBadgeById($id): Badge
    {
        return Badge::find($id);
    }

    public function delete($id): bool
    {
        $badge = Badge::find($id);

        return $badge->delete();
    }
}
