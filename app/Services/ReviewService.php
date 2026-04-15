<?php

namespace App\Services;

use Throwable;
use Carbon\Carbon;
use App\Enums\OrderStatus;
use App\Enums\ReviewStatus;
use App\Models\Review\Review;
use App\Models\Order\OrderItem;
use Illuminate\Support\Facades\DB;
use App\Exceptions\ReviewExpireException;
use App\Http\Resources\Ecommerce\MyReviewResource;

class ReviewService
{
    public function getReviews()
    {
        $reviews = auth()->user()->reviews()
            ->with([
                'user:id,name,phone',
                'user.media',
                'orderItem:id,product_variation_id,product_id,price,quantity',
                'orderItem.product_variant',
                'orderItem.product_variant.variationAttributes:id,attribute_option_id,attribute_id,product_variation_id',
                'orderItem.product_variant.variationAttributes.attribute:id,name',
                'orderItem.product_variant.variationAttributes.attributeOption:id,attribute_value',
                'orderItem.product',
            ])
            // ->where('is_public', ReviewStatus::IS_PUBLIC->value)
            ->select('id', 'rating', 'seller_rating', 'shipping_rating', 'review', 'order_item_id', 'user_id', 'created_at', 'order_item_id')->orderBy('created_at', 'desc')->paginate();

        return resourceFormatPagination('Review get successfully', MyReviewResource::collection($reviews), $reviews);
    }

    public function getToReviews()
    {
        $orders = auth()->user()->orders()
            ->with([
                'orderItems.product_variant:id',
                'orderItems.product_variant.variationAttributes:id,attribute_option_id,attribute_id,product_variation_id',
                'orderItems.product_variant.variationAttributes.attribute:id,name',
                'orderItems.product_variant.variationAttributes.attributeOption:id,attribute_value',
                'orderItems.product',
            ])
            ->whereHas('merchantOrders', function ($query) {
                $query->where('status_id', OrderStatus::DELIVERED->value);
            })
            ->where(function ($query) {
                $query->whereHas('orderItems', function ($query) {
                    $query->whereDoesntHave('review');
                });
            })
            ->latest()
            ->paginate();

        $toReview = $orders->getCollection()->flatMap(function ($order) {
            // an additional check to only include unreviewed items
            return $order->orderItems->filter(function ($item) {
                return ! $item->review;
            })->map(function ($item) {
                $thumbnail = $item->product->thumbnail;
                if (isset($item->product_variant) and $item->product_variant->image) {
                    $thumbnail = $item->product_variant->image;
                }

                return [
                    'id'                => intval($item->id),
                    'product_id'        => intval($item->product_id),
                    'product_name'      => $item->product->name,
                    'product_slug'      => $item->product->slug,
                    'product_sku'       => $item->product->sku,
                    'price'             => $item->price,
                    'quantity'          => $item->quantity,
                    'product_thumbnail' => $thumbnail,
                    'shop_id'           => intval($item->product?->merchant?->id),
                    'shop_name'         => $item->product?->merchant?->shop_name,
                    'shop_slug'         => $item->product?->merchant?->slug,
                    'product_variant'   => OrderService::getOrderItemVariantText($item->product_variant->variationAttributes ?? []),
                ];
            });
        });

        return response()->json([
            'message'       => 'to review get successfully',
            'data'          => $toReview,
            'total'         => $orders->total(),
            'last_page'     => $orders->lastPage(),
            'current_page'  => $orders->currentPage(),
            'next_page_url' => $orders->nextPageUrl(),
        ], 200);
    }

    public function getShopReviews($merchant)
    {
        $reviews = Review::query()
            ->whereHas('orderItem.product', function ($query) use ($merchant) {
                $query->where('merchant_id', $merchant->id);
            })
            ->with([
                'user:id,name,phone',
                'user.media',
                'orderItem:id,product_variation_id,product_id',
                'orderItem.product:id,name,slug',
                'orderItem.product_variant',
                'orderItem.product_variant.variationAttributes' => function ($query) {
                    $query->select('id', 'attribute_option_id', 'attribute_id', 'product_variation_id')
                        ->with([
                            'attribute:id,name',
                            'attributeOption:id,attribute_value',
                        ]);
                },
            ])
            ->orderBy('created_at', 'desc')
            ->paginate(request('per_page', 15));

        return resourceFormatPagination('Shop reviews get successfully', MyReviewResource::collection($reviews), $reviews);
    }

    /**
     * @throws Throwable
     */
    public function store($request)
    {
        DB::beginTransaction();

        try {
            $orderItem = OrderItem::find($request->order_item_id);
            $review    = $orderItem->review()->create([
                'product_id'      => $orderItem->product_id,
                'user_id'         => auth()->user()->id,
                'review'          => $request->review,
                'rating'          => $request->rating,
                'seller_rating'   => $request->seller_rating,
                'shipping_rating' => $request->shipping_rating,
                'is_approved'     => ReviewStatus::IS_APPROVED->value,
                'is_public'       => ReviewStatus::IS_NOT_PUBLIC->value,
            ]);

            $review->images = $request->images;
            $review->product->updateRating();
            DB::commit();

            return $review;
        } catch (Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }

    /**
     * @throws Throwable
     */
    public function updateReview($data, Review $review)
    {
        if (Carbon::parse($review->created_at)->lt(now()->subDays(3))) {
            throw new ReviewExpireException('You can only update a review within 3 days of submission.');
        }

        DB::beginTransaction();

        try {
            $review->update([
                'review'          => $data['review'],
                'rating'          => $data['rating'],
                'seller_rating'   => $data['seller_rating'],
                'shipping_rating' => $data['shipping_rating'],
            ]);

            // Delete media
            if (! empty($data['delete_image_ids'])) {
                foreach ($review->images as $image) {
                    if (in_array($image['id'], $data['delete_image_ids'])) {
                        $review->deleteMedia($image['id']);
                        logger("Deleted image ID: {$image['id']}");
                    }
                }
            }

            // Upload new media
            if (! empty($data['images'])) {
                $review->images = $data['images'];
            }

            $review->product->updateRating();

            DB::commit();

            return $review->fresh();
        } catch (Throwable $e) {
            DB::rollBack();
            logger()->error('Failed to update review', ['error' => $e->getMessage()]);

            throw $e;
        }
    }
}
