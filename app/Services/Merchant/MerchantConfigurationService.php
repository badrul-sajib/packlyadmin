<?php

namespace App\Services\Merchant;

use App\Models\Merchant\Merchant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class MerchantConfigurationService
{
    public function merchantConfiguration(array $data, Merchant $merchant)
    {
        DB::transaction(function () use ($data, $merchant) {

            $this->handleConfiguration(merchant: $merchant, data: $data);
            
            Cache::forget("merchant_{$merchant->id}_commission");

            $this->handleProductCommission(
                merchant: $merchant,
                product_ids: $data['product_ids'] ?? [],
                commission_rate: $data['product_commission_rate'] ?? [],
            );

        });
    }

    public function verification(array $data, Merchant $merchant)
    {
        $merchant->update($data);
    }

    protected function handleConfiguration(Merchant $merchant, array $data = [])
    {
        if (!empty($data)) {
            $merchant->configuration()->updateOrCreate(
                ['merchant_id' => $merchant->id],
                [
                    'min_amount'               => $data['min_amount'] ?? 0,
                    'per_day_request'          => $data['per_day_request'],
                    'payout_charge'            => $data['payout_charge'],
                    'maximum_product_request'  => $data['maximum_product_request'],
                    'commission_rate'          => $data['commission_rate'],
                    'payout_request_date'      => $data['payout_request_date'],
                    'id_delivery_fee'          => $data['id_delivery_fee'],
                    'od_delivery_fee'          => $data['od_delivery_fee'],
                    'ed_delivery_fee'          => $data['ed_delivery_fee'],
                ],
            );
        }
    }
 
    protected function handleProductCommission(Merchant $merchant, array $product_ids = [], array $commission_rate = [])
    {
        $merchant->productCommissions()->delete();

        if (!empty($product_ids) && !empty($commission_rate)) {
            foreach ($product_ids as $index => $productId) {

                $commission = $commission_rate[$index] ?? null;
                if ($commission === null || $productId === null) {
                    continue;
                }

                $merchant->productCommissions()->updateOrCreate(
                    [
                        'merchant_id' => $merchant->id,
                        'product_id' => $productId,
                    ],
                    [
                        'commission_rate' => $commission,
                    ],
                );
            }
        }
    }



}
