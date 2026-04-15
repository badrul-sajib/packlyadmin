<?php

namespace App\Services;

use App\Models\Review\Review;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class MerchantReviewService
{
    public function getAllReviews(): LengthAwarePaginator|\Illuminate\Pagination\LengthAwarePaginator|array
    {
        $perPage   = request('per_page', 10);
        $productId = request('product_id');

        return Review::with([
            'user.media',
            'product:id,name,merchant_id,slug',
            'product.media',
            'orderItem.product_variant',
            'orderItem.merchantOrder',
        ])
            ->whereHas('product', function ($query) {
                $query->where('merchant_id', Auth::user()->merchant->id);
            })
            ->when($productId, function ($query) use ($productId) {
                $query->where('product_id', $productId);
            })
            ->latest()->paginate($perPage);
    }

    public function replyStore(string $reply_message, int $id): array|Collection|Model|Review|null
    {
        $review               = Review::find($id);
        $review->review_reply = $reply_message;
        $review->save();

        return $review;
    }
}
