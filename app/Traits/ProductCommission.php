<?php

namespace App\Traits;

use App\Caches\ShopSettingsCache;
use App\Models\Merchant\Merchant;
use App\Models\Product\Product;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

trait ProductCommission
{
    protected $cacheDuration = 7;

    public function getProductCommission(Product $product): ?object
    {
        try {

            $commission = null;
            $commission_type = 'percent';

            if ($product->merchantCommission && $commission === null) {
                $commission = $product->merchantCommission->commission_rate ?? null;
            }

            if ($product->sub_category_child_id && $commission === null) {
                $commission      = $product->subCategoryChild->commission ?? null;
                $commission_type = $product->subCategoryChild->commission_type ?? 'percent';
            }

            if ($product->sub_category_id && $commission === null) {
                $commission      = $product->subCategory->commission ?? null;
                $commission_type = $product->subCategory->commission_type ?? 'percent';
            }

            if ($product->category_id && $commission === null) {
                $commission      = $product->category->commission ?? null;
                $commission_type = $product->category->commission_type ?? 'percent';
            }


            if ($commission === null) {
                $key = "merchant_{$product->merchant->id}_commission";
                $commission = Cache::remember($key, now()->addDays($this->cacheDuration), function () use ($product) {
                    return $product->merchant->configuration->commission_rate ?? null;
                });
            }            

            if ($commission === null) {
                $commission = ShopSettingsCache::findByKey('commission_rate');
            }

            return (object) [
                'commission'       => $commission,
                'commission_type'  => $commission_type
            ];

        } catch (\Exception $e) {
            Log::error('Error fetching product commission: ' . $e->getMessage());

            return null;
        }
    }


    public function getCommission($type = 'percent', $commission = 0, int $value = 0): mixed
    {
        try {
            if ($type === 'fixed') {
                return $commission;
            }

            if($commission  == null) {
                $commission = 0;
            }

            return ($value * $commission) / 100;
        } catch (\Throwable $th) {
            return 0;
        }
    }
}
