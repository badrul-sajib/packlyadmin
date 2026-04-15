<?php

namespace App\Services;

use App\Models\Reel\Reel;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class ReelService
{
    public function getAllReels(Request $request): LengthAwarePaginator
    {
        $status = $request->input('status', null);

        return Reel::query()
            ->when($request->has('search'), function ($query) use ($request) {
                $query->whereAny(['title', 'link'], 'like', '%'.$request->search.'%');
            })
            ->when($status, function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->orderBy('created_at', 'desc')
            ->paginate($request->per_page ?? 10);
    }

    public function updateReelStatus(int $id, string $status): ?Reel
    {
        $reel = Reel::find($id);

        $merchant = $reel?->merchant;

        if ($merchant && $reel->status === 'pending' && $status === 'active') {
            $merchant->sendNotification(
                'Reel Approved',
                'Your reel has been approved and is now live.',
                '/reels'
            );
        }

        if ($merchant && $reel->status === 'pending' && $status === 'rejected') {
            $merchant->sendNotification(
                'Reel Rejected',
                'Your reel has been rejected.',
                '/reels'
            );
        }

        if ($merchant && $reel->status === 'active' && $status === 'inactive') {
            $merchant->sendNotification(
                'Reel Inactive',
                'Your reel has been set to inactive by the admin.',
                '/reels'
            );
        }

        if ($merchant && $reel->status === 'inactive' && $status === 'active') {
            $merchant->sendNotification(
                'Reel Active',
                'Your reel has been reactivated by the admin.',
                '/reels'
            );
        }

        if ($reel) {
            $data = [
                'status' => $status,
            ];
            $reel->update($data);
        }

        return $reel;
    }
}
