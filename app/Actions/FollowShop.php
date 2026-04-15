<?php

namespace App\Actions;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class FollowShop
{
    public function execute($request): JsonResponse
    {
        try {
            $user = auth()->user();

            $isFollowing = $user->followedMerchants()->where('merchant_id', $request->id)->exists();

            if ($isFollowing) {
                $user->followedMerchants()->detach($request->id);

                return success('Shop unfollowed successfully', [
                    'status' => 'unfollowed',
                ]);
            } else {
                $user->followedMerchants()->attach($request->id);

                return success('Shop followed successfully', [
                    'status' => 'followed',
                ]);
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return failure('Something went wrong', 500);
        }
    }
}
