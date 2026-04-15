<?php

namespace App\Services\Merchant;

use App\Models\Reel\Reel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class ReelService
{
    public function getAllReels(Request $request): LengthAwarePaginator
    {
        $merchantId = auth()->user()->merchant->id;

        return Reel::query()
            ->with(['merchant:id,slug,shop_name', 'media'])
            ->withCount(['reelUserActions as reaction_count' => function ($query) {
                $query->where('action_type', 'like');
            }])
            ->where('merchant_id', $merchantId)
            ->when($request->has('search'), function ($query) use ($request) {
                $query->where('title', 'like', '%'.$request->search.'%');
            })
            ->when($request->filled('days'), function ($query) use ($request) {
                $days = (int) $request->input('days');

                if (in_array($days, [1, 3, 7, 15, 30])) {
                    $startDate = now()->subDays($days);
                    $query->where('created_at', '>=', $startDate);
                }
            })
            ->orderByDesc('created_at')
            ->paginate($request->per_page ?? 10);
    }

    public function getReelById(int $id): Builder|array|Collection|Model
    {
        $merchantId = auth()->user()->merchant->id;

        return Reel::query()
            ->where('merchant_id', $merchantId)
            ->find($id);
    }

    public function createReel(array $data): Reel
    {

        $merchantId = auth()->user()->merchant->id;

        $reel              = new Reel;
        $reel->title       = $data['title'] ?? '';
        $reel->link        = $data['link'];
        $reel->description = $data['description'];
        $reel->merchant_id = $merchantId;
        $reel->status      = 'pending';

        $reel->enable_buy_now_button = $data['enable_buy_now_button'] ?? false;

        if ($reel->enable_buy_now_button) {
            $reel->buy_now_type = $data['buy_now_type'] ?? null;
            $reel->product_id   = $data['buy_now_type'] === 'product' ? ($data['product_id'] ?? null) : null;
        } else {
            $reel->buy_now_type = null;
            $reel->product_id   = null;
        }
        $reel->save();

        return $reel;
    }

    public function updateReel(int $id, array $data): ?Reel
    {
        $merchantId = auth()->user()->merchant->id;

        $reel = Reel::find($id);
        if (! $reel || $reel->merchant_id !== $merchantId) {
            throw new ModelNotFoundException('Reel not found or does not belong to the current merchant.');
        }

        $reel->title       = $data['title'];
        $reel->link        = $data['link'];
        $reel->description = $data['description'];

        $reel->enable_buy_now_button = $data['enable_buy_now_button'] ?? false;

        if ($reel->enable_buy_now_button) {
            $reel->buy_now_type = $data['buy_now_type'] ?? null;
            $reel->product_id   = $data['buy_now_type'] === 'product' ? ($data['product_id'] ?? null) : null;
        } else {
            $reel->buy_now_type = null;
            $reel->product_id   = null;
        }

        $reel->save();

        return $reel;
    }

    public function deleteReel(int $id): bool
    {
        $merchantId = auth()->user()->merchant->id;

        $reel = Reel::where('merchant_id', $merchantId)->find($id);

        if ($reel) {
            $reel->deleteMediaCollection('image');
            $reel->delete();

            return true;
        }

        return false;
    }
}
