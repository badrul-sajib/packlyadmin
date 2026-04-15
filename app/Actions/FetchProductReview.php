<?php

namespace App\Actions;

use App\Models\Product\Product;
use App\Models\Shop\ShopSetting;
use App\Services\OrderService;
use Illuminate\Http\Response;

class FetchProductReview
{
    public function execute($request, $slug)
    {
        $product = Product::with('reviews')->where('slug', $slug)->first();
        if (! $product) {
            return failure('Product not found', 404);
        }
        $perPage = request('per_page') ?? 10;

        $reviewsQuery = $product->reviews()
            ->with([
                'user:id,name,phone',
                'user.media',
                'orderItem:id,product_variation_id',
                'orderItem.product_variant:id',
                'orderItem.product_variant.variationAttributes:id,attribute_option_id,attribute_id,product_variation_id',
                'orderItem.product_variant.variationAttributes.attribute:id,name',
                'orderItem.product_variant.variationAttributes.attributeOption:id,attribute_value',
            ])
            ->select('id', 'rating', 'review', 'order_item_id', 'user_id', 'review_reply', 'created_at', 'order_item_id', 'updated_at');

        if ($request->has('with_image')) {
            $reviewsQuery->whereHas('media', function ($query) {
                $query->where('collection_name', 'images');
            });
        }

        $reviews = $reviewsQuery->paginate($perPage);

        // Use map to combine all orderItems into a single collection
        $toReview = $reviews->getCollection()->map(function ($review) {
            return [
                'id'                => $review->id,
                'rating'            => $review->rating,
                'review'            => $review->review,
                'seller_reply'      => $review->review_reply ?? null,
                'user_name'         => $review->user->name,
                'user_phone'        => $review->user->phone,
                'user_avatar'       => $review->user->avatar ?: null,
                'feedback_images'   => $review->images,
                'product_variant'   => OrderService::getOrderItemVariantText($review->orderItem->product_variant->variationAttributes ?? []),
                'created_at'        => $review->created_at,
                'seller_reply_date' => $review->review_reply ? $review->updated_at : null,
            ];
        });

        $textData = ShopSetting::whereIn('key', [
            'assured_text',
            'authentic_text',
            'assured_description',
        ])->pluck('value', 'key')->toArray();

        return response()->json([
            'message'       => 'Product reviews fetched successfully',
            'text_data'     => $textData,
            'data'          => $toReview,
            'total'         => $reviews->total(),
            'last_page'     => $reviews->lastPage(),
            'current_page'  => $reviews->currentPage(),
            'next_page_url' => $reviews->nextPageUrl(),
        ], Response::HTTP_OK);
    }
}
