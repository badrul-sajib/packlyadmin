<?php

namespace App\Services\Coupon;

use App\Models\Coupon\Coupon;
use App\Models\Coupon\CouponProductVariant;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Throwable;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class CouponServices
{
    public function getAllCoupons($request)
    {
        $perPage       = $request->perPage       ?? 10;
        $search        = $request->search        ?? null;
        $discount_type = $request->discount_type ?? null;
        $page          = $request->page          ?? 1;
        $startDate     = $request->start_date ? date('Y-m-d 00:00:00', strtotime($request->start_date)) : null;
        $endDate       = $request->end_date ? date('Y-m-d 23:59:59', strtotime($request->end_date)) : null;

        return Coupon::query()
            ->with('merchants', 'user')
            ->when($search, function ($query) use ($search) {
                $query->whereAny(['name', 'code'], 'like', '%'.$search.'%');
            })
            ->when($discount_type, function ($query) use ($discount_type) {
                $query->where('discount_type', $discount_type);
            })
            ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            })
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

            $bear_by_packly = null ;

            if ($data->apply_on == 'shipping_charge') {
                $bear_by_packly = 1;
            }else{
                $bear_by_packly = $data->bear_by_packly ? intval($data->bear_by_packly) : null;
            }

            $coupon = Coupon::create([
                'name'                 => $data->name,
                'apply_on'             => $data->apply_on,
                'code'                 => $data->code,
                'discount_value'       => $data->discount_value,
                'max_discount_value'   => $data->max_discount_value,
                'description'          => $data->description,
                'min_purchase'         => $data->min_purchase,
                'max_purchase'         => $data->max_purchase,
                'usage_limit_per_user' => $data->usage_limit_per_user,
                'usage_limit_total'    => $data->usage_limit_total,
                'discount_type'        => $data->type,
                'status'               => $data->status,
                'start_date'           => $data->start_date,
                'end_date'             => $data->end_date,
                'merchant_type'        => $data->merchant_type,
                'category_type'        => $data->category_type,
                'brand_type'           => $data->brand_type,
                'product_type'         => $data->product_type,
                'bear_by_packly'       => $bear_by_packly,
                'added_by'             => auth()->id(),
            ]);

            // Attach related entities if they exist
            if (! empty($data->merchant_ids)) {
                $coupon->merchants()->attach($data->merchant_ids);
            }

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

            DB::commit();

            activity()
                ->useLog('coupon-create')
                ->event('created')
                ->performedOn($coupon)
                ->causedBy(auth()->user())
                ->withProperties([
                    'created_coupon' => $coupon->name,
                ])
                ->log('Coupon created by '.auth()->user()->name);

            return response()->json(['message' => 'Coupon created successfully']);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(['message' => 'Failed to create coupon'], 500);
        }
    }

    public function getCouponById($id): Coupon
    {
        return Coupon::findOrFail($id);
    }

    public function getCouponUsages(int $id)
    {
        $coupon = $this->getCouponById($id);
        return $coupon->couponUsages()
            ->with(['merchantOrder', 'user'])
            ->latest()
            ->paginate(10);
    }

    public function getCouponStats(int $id): array
    {
        $coupon = $this->getCouponById($id);

        // Stats
        $usages = $coupon->couponUsages()->with(['order', 'merchantOrder'])->get();
        
        $totalUsage            = $usages->count();
        $totalDiscountAll      = 0;
        $totalDiscountByPackly = 0;

        foreach ($usages as $usage) {
            $amount = $usage->discount_amount;

            if ($usage->discount_type) {
                
                if ($usage->discount_type === 'percentage') {
                    $totalAmount = 0;
                    
                    if($usage->order_id){
                        $totalAmount = $usage->order->total_amount;
                    }else{
                        $totalAmount = $usage->merchantOrder->total_amount;
                    }

                    $amount = ($amount * $totalAmount) / 100;

                    if ($usage->max_discount < $amount) {
                        $amount = min($usage->max_discount,$amount);
                    }
                } 

               
            }

            $totalDiscountAll += $amount;

            // Packly Share Logic
            if ($usage->bear_by_packly == 1 || $usage->coupon_type === 'shipping_charge') {
                $totalDiscountByPackly += $amount;
            }
        }

        $uniqueUsers = $usages->unique('user_id')->count();

        // Payment Split
        $paidByPackly   = $totalDiscountByPackly;
        $paidByMerchant = $totalDiscountAll - $paidByPackly;

        // Chart Data (Coupon Validity Period)
        $startDate = Carbon::parse($coupon->start_date);
        $endDate   = Carbon::parse($coupon->end_date);
        $period    = CarbonPeriod::create($startDate, $endDate);

        $usageData = $coupon->couponUsages()
            ->whereBetween('created_at', [$startDate->startOfDay(), $endDate->endOfDay()])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->pluck('count', 'date');

        $chartCategories = [];
        $chartSeries = [];

        foreach ($period as $date) {
            $formattedDate = $date->format('Y-m-d');
            $chartCategories[] = $date->format('d M');
            $chartSeries[] = $usageData[$formattedDate] ?? 0;
        }

        return [
            'totalUsage'      => $totalUsage,
            'totalDiscount'   => $totalDiscountAll,
            'uniqueUsers'     => $uniqueUsers,
            'paidByPackly'    => $paidByPackly,
            'paidByMerchant'  => $paidByMerchant,
            'chartCategories' => $chartCategories,
            'chartSeries'     => $chartSeries,
        ];
    }

    /**
     * @throws Throwable
     */
    public function updateCoupon($data, $id): JsonResponse
    {
        DB::beginTransaction();

        try {

            
            // Find the coupon by ID
            $coupon = Coupon::findOrFail($id);

            // if cupon status is pending
            if ($coupon->status == 'pending' && $data['status'] == 'active') {
                // send notification to merchants
                $merchants = $coupon->merchants;

                foreach ($merchants as $merchant) {
                    $merchant->sendNotification(
                        'Coupon Approved',
                        'Your coupon has been approved! You can now use it.',
                        '/coupon/'.$coupon->id
                    );
                }
            }

            if ($coupon->status == 'active' && $data['status'] == 'inactive') {
                // send notification to merchants
                $merchants = $coupon->merchants;
                foreach ($merchants as $merchant) {
                    $merchant->sendNotification(
                        'Coupon Inactive',
                        'Your coupon has been disabled by admin!',
                        '/coupon/'.$coupon->id
                    );
                }
            }

            if ($coupon->status == 'inactive' && $data['status'] == 'active') {
                // send notification to merchants
                $merchants = $coupon->merchants;
                foreach ($merchants as $merchant) {
                    $merchant->sendNotification(
                        'Coupon Inactive',
                        'Your coupon has been enabled by admin!',
                        '/coupon/'.$coupon->id
                    );
                }
            }


            $bear_by_packly = null ;
            if ($data->apply_on == 'shipping_charge') {
                $bear_by_packly = 1;
            }else{
                $bear_by_packly = $data['bear_by_packly'] ? intval($data['bear_by_packly']) : null;
            }


            // Update coupon with validated data
            $coupon->fill([
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
                'merchant_type'        => $data['merchant_type'],
                'category_type'        => $data['category_type'],
                'brand_type'           => $data['brand_type'],
                'product_type'         => $data['product_type'],
                'bear_by_packly'       => $bear_by_packly,
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

            // Update related entities (merchants, categories, brands, products)
            if (! empty($data['merchant_ids'])) {
                $coupon->merchants()->sync($data['merchant_ids']);
            }

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

            $properties = getModelChanges($coupon);
            $coupon->save();

            if (! blank($properties['new'])) {
                activity()
                    ->useLog('coupon-update')
                    ->event('updated')
                    ->performedOn($coupon)
                    ->causedBy(auth()->user())
                    ->withProperties($properties)
                    ->log('Coupon updated by '.auth()->user()->name);
            }

            DB::commit();

            return response()->json(['message' => 'Coupon updated successfully']);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(['message' => 'Failed to update coupon' .$e->getMessage()], 500);
        }
    }

    public function deleteCoupon($id): void
    {
        $coupon = $this->getCouponById($id);

        // send notification to merchants
        $merchants = $coupon->merchants;
        foreach ($merchants as $merchant) {
            $merchant->sendNotification(
                'Coupon Deleted',
                'Your coupon has been deleted by admin!',
                '/coupon/'.$coupon->id
            );
        }

        activity()
            ->useLog('coupon-delete')
            ->event('deleted')
            ->performedOn($coupon)
            ->causedBy(auth()->user())
            ->withProperties([
                'deleted_coupon' => $coupon->name,
            ])
            ->log('Coupon deleted by '.auth()->user()->name);

        $coupon->delete();
    }
}
