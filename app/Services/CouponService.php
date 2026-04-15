<?php

namespace App\Services;

use App\Jobs\PushNotification;
use App\Models\Coupon\Coupon;
use App\Models\Coupon\CouponProductVariant;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class CouponService
{
    public function getAllCoupons($request): LengthAwarePaginator|\Illuminate\Pagination\LengthAwarePaginator|array
    {
        $perPage       = $request->perPage       ?? 10;
        $search        = $request->search        ?? null;
        $discount_type = $request->discount_type ?? null;
        $page          = $request->page          ?? 1;
        $startDate     = $request->start_date ? date('Y-m-d 00:00:00', strtotime($request->start_date)) : null;
        $endDate       = $request->end_date ? date('Y-m-d 23:59:59', strtotime($request->end_date)) : null;

        $merchant_id = auth()->user()->merchant->id;

        return Coupon::query()
            ->with('merchants', 'user')
            ->whereHas('merchants', function ($query) use ($merchant_id) {
                $query->where('merchants.id', $merchant_id);
            })
            ->when($search, function ($query) use ($search) {
                $query->whereAny(['name', 'code'], 'like', '%'.$search.'%');
            })
            ->when($discount_type, function ($query) use ($discount_type) {
                $query->where('discount_type', $discount_type);
            })
            ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            })
            ->where('is_admin', false)
            ->latest()
            ->paginate($perPage, ['*'], 'page', $page)
            ->withQueryString();
    }

    /**
     * @throws Throwable
     */
    public function createCoupon($data): JsonResponse
    {
        DB::beginTransaction();

        try {
            $coupon = Coupon::create([
                'name'                 => $data->name,
                'code'                 => $data->code,
                'discount_value'       => $data->discount_value,
                'max_discount_value'   => $data->max_discount_value,
                'description'          => $data->description,
                'min_purchase'         => $data->min_purchase ?? 0,
                'max_purchase'         => $data->max_purchase ?? 0,
                'usage_limit_per_user' => $data->usage_limit_per_user,
                'usage_limit_total'    => $data->usage_limit_total,
                'discount_type'        => $data->type,
                'status'               => 'pending',
                'start_date'           => $data->start_date,
                'end_date'             => $data->end_date,
                'merchant_type'        => 2,
                'category_type'        => $data->category_type,
                'brand_type'           => $data->brand_type,
                'product_type'         => $data->product_type,
                'added_by'             => auth()->user()->id,
                'is_admin'             => false,
            ]);

            $coupon->merchants()->attach([$data->merchant_id]);

            if (! empty($data->category_ids)) {
                $coupon->categories()->attach($data->category_ids);
            }

            if (! empty($data->brand_ids)) {
                $coupon->brands()->attach($data->brand_ids);
            }

            if (! empty($data->product_ids)) {
                $coupon->products()->attach($data->product_ids);

                foreach ($data->product_ids as $productId) {
                    if (isset($data->varient[$productId]) && is_array($data->varient[$productId])) {
                        foreach ($data->varient[$productId] as $variantId) {
                            CouponProductVariant::create([
                                'coupon_id'            => $coupon->id,
                                'product_id'           => $productId,
                                'product_variation_id' => $variantId,
                            ]);
                        }
                    }
                }
            }

            $notificationMessage = 'New coupon requested by '.auth()->user()->name.'.';

            DB::commit();

            try {
                PushNotification::dispatch([
                    'title'      => 'New Coupon Request',
                    'message'    => $notificationMessage,
                    'type'       => 'info',
                    'action_url' => '/coupons/'.$coupon->id.'/edit',
                ]);
            } catch (Throwable $th) {
                Log::error($th->getMessage());
            }

            return ApiResponse::successMessageForCreate('Coupon Request Sent to the Admin', $coupon, Response::HTTP_CREATED);
        } catch (\Exception $e) {
            DB::rollBack();

            return ApiResponse::error('Failed to create coupon', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getCouponById(int $id): Coupon
    {
        return Coupon::findOrFail($id);
    }

    /**
     * @throws Throwable
     */
    public function updateCoupon($data, int $id): JsonResponse
    {
        DB::beginTransaction();

        try {
            // Find the coupon by ID
            $coupon = Coupon::findOrFail($id);

            // if is_admin is true return
            if ($coupon->is_admin) {
                return response()->json(['message' => 'Cannot update admin coupon'], Response::HTTP_FORBIDDEN);
            }

            // added_by is not allowed to update
            if ($coupon->added_by != auth()->user()->merchant->id) {
                return response()->json(['message' => 'Cannot update this coupon'], Response::HTTP_FORBIDDEN);
            }

            // Update coupon with validated data
            $coupon->update([
                'name'                 => $data['name'],
                'code'                 => $data['code'],
                'discount_value'       => $data['discount_value'],
                'max_discount_value'   => $data['max_discount_value'],
                'description'          => $data['description'],
                'min_purchase'         => $data['min_purchase'],
                'max_purchase'         => $data['max_purchase'],
                'usage_limit_per_user' => $data['usage_limit_per_user'],
                'usage_limit_total'    => $data['usage_limit_total'],
                'discount_type'        => $data['type'],
                'status'               => $data['status'],
                'start_date'           => $data['start_date'],
                'end_date'             => $data['end_date'],
                'merchant_type'        => 2,
                'category_type'        => $data['category_type'],
                'brand_type'           => $data['brand_type'],
                'product_type'         => $data['product_type'],
            ]);

            // Before syncing the products, delete existing variants for those products
            if (! empty($data['product_ids'])) {
                // Get the current product ids that are associated with this coupon
                $currentProductIds = $coupon->products->pluck('id')->toArray();

                // Find the product IDs that are no longer associated with this coupon (to be removed)
                $removedProductIds = array_diff($currentProductIds, $data['product_ids']);

                // Delete the product variants for the removed products
                foreach ($removedProductIds as $productId) {
                    CouponProductVariant::where('coupon_id', $coupon->id)
                        ->where('product_id', $productId)
                        ->delete();
                }

                // Sync the products
                $coupon->products()->sync($data['product_ids']);

                // Attach product variants for the coupon
                foreach ($data['product_ids'] as $productId) {
                    if (isset($data['varient'][$productId]) && is_array($data['varient'][$productId])) {
                        foreach ($data['varient'][$productId] as $variantId) {
                            CouponProductVariant::create([
                                'coupon_id'            => $coupon->id,
                                'product_id'           => $productId,
                                'product_variation_id' => $variantId,
                            ]);
                        }
                    }
                }
            }

            // Update related entities (categories, brands, products)

            if (! empty($data['category_ids'])) {
                $coupon->categories()->sync($data['category_ids']);
            }

            if (! empty($data['brand_ids'])) {
                $coupon->brands()->sync($data['brand_ids']);
            }

            if (empty($data['product_type'])) {
                $coupon->products()->detach();
                $coupon->productVariants()->delete();
            }

            if (empty($data['category_type'])) {
                $coupon->categories()->detach();  // Detach categories
            }

            if (empty($data['brand_type'])) {
                $coupon->brands()->detach();  // Detach brands
            }

            if (empty($data['merchant_type'])) {
                $coupon->merchants()->detach();  // Detach merchants
            }

            DB::commit();

            return ApiResponse::success('Coupons updated successfully', [], Response::HTTP_OK);
        } catch (\Exception $e) {
            DB::rollBack();

            return ApiResponse::failure('Failed to update coupon', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function deleteCoupon(int $id): bool
    {
        $coupon = $this->getCouponById($id);
        $coupon->delete();

        return true;
    }
}
