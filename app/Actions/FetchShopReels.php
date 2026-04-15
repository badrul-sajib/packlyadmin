<?php

namespace App\Actions;

use App\Enums\MerchantStatus;
use App\Models\Merchant\MerchantFollower;
use App\Models\Reel\Reel;
use App\Models\Reel\ReelUserAction;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class FetchShopReels
{
    public function execute(): JsonResponse
    {

        $reels = Reel::with(['shop', 'product:id,name'])
            ->where('status', 'active')
            ->latest()
            ->get();

        $user = auth('sanctum')->user();

        $data = $reels
            ->filter(function ($reel) use ($user) {
                if ($user) {
                    $isBlocked = ReelUserAction::where('user_id', $user->id)
                        ->where('reel_id', $reel->id)
                        ->where('action_type', 'block')
                        ->exists();

                    return ! $isBlocked;
                }

                return true;
            })
            ->filter(function ($reel) {
                return $reel->shop?->shop_status == MerchantStatus::Active;
            })
            ->values()
            ->map(function ($reel) use ($user) {
                // Default values
                $isLiked    = false;
                $isBlocked  = false;
                $isFollowed = false;

                if ($user) {
                    $isLiked = ReelUserAction::where('user_id', $user->id)
                        ->where('reel_id', $reel->id)
                        ->where('action_type', 'like')
                        ->exists();

                    $isBlocked = ReelUserAction::where('user_id', $user->id)
                        ->where('reel_id', $reel->id)
                        ->where('action_type', 'block')
                        ->exists();

                    $isFollowed = MerchantFollower::where('user_id', $user->id)
                        ->where('merchant_id', $reel->merchant_id)
                        ->exists();

                }

                return [
                    'id'                    => $reel->id,
                    'title'                 => $reel->title,
                    'link'                  => $reel->link,
                    'product_name'          => $reel->product?->name ?? null,
                    'description'           => $reel->description,
                    'merchant_id'           => $reel->merchant_id,
                    'status'                => $reel->status,
                    'created_at'            => $reel->created_at,
                    'updated_at'            => $reel->updated_at,
                    'image'                 => $reel->image,
                    'shop_id'               => $reel->merchant_id      ?? null,
                    'shop_logo'             => $reel->shop?->shop_logo ?? null,
                    'shop_name'             => $reel->shop?->shop_name ?? null,
                    'shop_slug'             => $reel->shop?->slug      ?? ($reel->shop?->shop_name ? Str::slug($reel->shop->shop_name) : ''),
                    'time_ago'              => Carbon::parse($reel->updated_at)->diffForHumans(),
                    'followers'             => $reel->shop?->followers()->count() ?? 0,
                    'is_liked'              => $isLiked,
                    'is_blocked'            => $isBlocked,
                    'is_followed'           => $isFollowed,
                    'enable_buy_now_button' => $reel->enable_buy_now_button,
                    'buy_now_type'          => $reel->buy_now_type,
                    'product_id'            => $reel->product_id,
                ];

            });

        return success('Reels fetched successfully', $data);
    }

    // -----------------new version but wait for testing and frontend development it's working fine ----------\\
    // public function execute(): JsonResponse
    // {

    //     $reels = Reel::with(['shop', 'product:id,name,slug','media'])
    //         ->where('status', 'active')
    //         ->latest()
    //         ->get();

    //     $user = auth('sanctum')->user();

    //     $data = $reels
    //         ->filter(function ($reel) use ($user) {
    //             if ($user) {
    //                 $isBlocked = ReelUserAction::where('user_id', $user->id)
    //                     ->where('reel_id', $reel->id)
    //                     ->where('action_type', 'block')
    //                     ->exists();

    //                 return ! $isBlocked;
    //             }

    //             return true;
    //         })
    //         ->filter(function ($reel) {
    //             return $reel->shop?->shop_status == MerchantStatus::Active;
    //         })
    //         ->values()
    //         ->map(function ($reel) use ($user) {
    //             // Default values
    //             $isLiked    = false;
    //             $isBlocked  = false;
    //             $isFollowed = false;
    //             $isVideo    = false;
    //             $media      = collect($reel->media)->filter(function ($m) {
    //                 return in_array($m->collection_name, ['image', 'video']);
    //             })->first();

    //             if ($user) {
    //                 $isLiked = ReelUserAction::where('user_id', $user->id)
    //                     ->where('reel_id', $reel->id)
    //                     ->where('action_type', 'like')
    //                     ->exists();

    //                 $isBlocked = ReelUserAction::where('user_id', $user->id)
    //                     ->where('reel_id', $reel->id)
    //                     ->where('action_type', 'block')
    //                     ->exists();

    //                 $isFollowed = MerchantFollower::where('user_id', $user->id)
    //                     ->where('merchant_id', $reel->merchant_id)
    //                     ->exists();

    //             }

    //             if ($media && $media->file_type) {
    //                 $isVideo = $media && Str::startsWith($media?->file_type, 'video/');
    //             }

    //             return [
    //                 'id'                    => $reel->id,
    //                 'title'                 => $reel->title,
    //                 'status'                => $reel->status,

    //                 'content'               => [
    //                     'title'       => $reel->title,
    //                     'slug'        => $reel->slug,
    //                     'description' => $reel->description,
    //                     'created_at'  => $reel->created_at,
    //                     'updated_at'  => $reel->updated_at,
    //                     'time_ago'    => Carbon::parse($reel->updated_at)->diffForHumans(),
    //                 ],

    //                 'media'                 => [
    //                     'id'            => $media->id ?? null,
    //                     'type'          => $reel->image ? 'image' : 'video',
    //                     'url'           => $isVideo ? $reel->video : $reel->image,
    //                     'thumbnail_url' => $reel->thumbnail_image ?? null,
    //                     'mime_type'     => $media->file_type ?? null,
    //                 ],

    //                 'shop'                  => [
    //                     'shop_id'       => $reel->merchant_id,
    //                     'shop_name'     => $reel->shop?->shop_name ?? null,
    //                     'shop_slug'     => $reel->shop?->slug ?? null,
    //                     'shop_logo'     => $reel->shop?->logo ?? null,
    //                     'followers'     => $reel->shop?->followers()->count() ?? 0,
    //                     'is_followed'   => $isFollowed,
    //                     'is_blocked'    => $isBlocked
    //                 ],
    //                 'engagement'            => [
    //                     'is_liked'      => $isLiked,
    //                 ],
    //                 'commerce' => [
    //                     'product_slug'      => $reel->product?->slug ?? null,
    //                     'product_name'      => $reel->product?->name ?? null,
    //                     'enable_buy_now'    => $reel->enable_buy_now_button,
    //                     'buy_now_type'      => $reel->buy_now_type,
    //                     'product_id'        => $reel->product_id,
    //                 ],
    //             ];

    //         });

    //     return success('Reels fetched successfully', $data);
    // }
}



