<?php

namespace App\Services\Checkout\Calculators;

use App\Caches\ShopSettingsCache;
use App\Enums\DeliveryType;
use App\Models\Order\CustomerAddress;
use App\Models\Product\Product;
use App\Services\InsideDhakaService;
use App\Support\CalculateWeightBasedCharge;

class ShippingCalculator
{
    public function calculate(CustomerAddress $address, array $orderItems, array $deliveryType): array
    {
        $shippingSettings = ShopSettingsCache::select(
            'shipping_fee_osd',
            'shipping_fee_isd',
            'shipping_additional_fee_isd',
            'shipping_additional_fee_osd',
        );

        $shippingType = self::determineShippingType($address);

        $merchantShippingDetails = [];
        $totalShippingFee = 0;

        foreach ($orderItems as $merchantId => $items) {
            $hasNonFreeShipping = false;
            $totalWeight = 0;

            $isExpressDelivery = ($deliveryType[$merchantId] ?? null) == 2;

            foreach ($items as $product) {

                if (isset($product['product_data']) && $product['product_data']->weight) {
                    $totalWeight += ($product['quantity'] * floatval($product['product_data']->weight)) ?? 0;
                }

                if ($isExpressDelivery || ! $product['free_shipping']) {
                    $hasNonFreeShipping = true;
                }
            }

            $newShippingFee = CalculateWeightBasedCharge::run(
                totalWeight: $totalWeight,
                shippingType: $shippingType,
                isd_fee: $shippingSettings->shipping_fee_isd,
                osd_fee: $shippingSettings->shipping_fee_osd,
                additional_isd_fee: $shippingSettings->shipping_additional_fee_isd,
                additional_osd_fee: $shippingSettings->shipping_additional_fee_osd,
            );

            $merchantShippingDetails[$merchantId] = [
                'has_free_shipping' => ! $hasNonFreeShipping,
                'delivery_charge' => $hasNonFreeShipping ? $newShippingFee : 0,
                'delivery_type' => $deliveryType[$merchantId],
                'total_weight' => $totalWeight,
            ];

            if ($hasNonFreeShipping) {
                $totalShippingFee += $newShippingFee;
            }

            $totalWeight = 0;
        }

        return [
            'shipping_type' => $shippingType,
            'total_shipping_fee' => $totalShippingFee,
            'merchant_shipping_details' => $merchantShippingDetails,
        ];
    }

    public static function getProductDeliveryCharge(
        int $productId,
        ?int $variationId,
        string $shippingType,
        ?string $deliveryType
    ): float {
        $key = match (true) {

            $deliveryType == DeliveryType::EXPRESS->value => 'ed_delivery_fee',
            $shippingType === 'ISD' => 'id_delivery_fee',
            default => 'od_delivery_fee'
        };

        $merchant = Product::where('id', $productId)
            ->with('merchant.configuration') // eager load configuration
            ->select('id', 'merchant_id')
            ->first()?->merchant;

        return (float) $merchant->getDeliveryCharges()[$key] ?? 0;
    }

    // TODO: Refactor this method to use the new InsideDhakaService
    // old method link: https://tinyurl.com/wsyyf8m2
    public static function determineShippingType(CustomerAddress $address): string
    {
        $addr = $address->address;

        if (preg_match('/[\x80-\xff]/', $addr)) {
            $addr = banglaToBanglish($addr);
        }

        $insideDhaka = (new InsideDhakaService)->isInsideDhaka($addr);

        return $insideDhaka ? 'ISD' : 'OSD';
    }

    public function calculateWithLocation($shippingType, array $orderItems, array $deliveryType): array
    {
        $merchantShippingDetails = [];
        $totalShippingFee = 0;

        foreach ($orderItems as $merchantId => $items) {
            $maxDeliveryCharge = 0;
            $hasNonFreeShipping = false;

            $isExpressDelivery = ($deliveryType[$merchantId] ?? null) == 2;

            foreach ($items as $product) {

                if ($isExpressDelivery || ! $product['free_shipping']) {
                    $hasNonFreeShipping = true;

                    $deliveryCharge = $this->getProductDeliveryCharge(
                        $product['product_id'],
                        $product['product_variation_id'],
                        $shippingType,
                        $deliveryType[$merchantId] ?? null
                    );

                    $maxDeliveryCharge = max($maxDeliveryCharge, $deliveryCharge);
                }
            }

            $merchantShippingDetails[$merchantId] = [
                'has_free_shipping' => ! $hasNonFreeShipping,
                'delivery_charge' => $hasNonFreeShipping ? $maxDeliveryCharge : 0,
                'delivery_type' => $deliveryType[$merchantId],
            ];

            if ($hasNonFreeShipping) {
                $totalShippingFee += $maxDeliveryCharge;
            }
        }

        return [
            'shipping_type' => $shippingType,
            'total_shipping_fee' => $totalShippingFee,
            'merchant_shipping_details' => $merchantShippingDetails,
        ];
    }
}
