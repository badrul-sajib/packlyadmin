<?php

namespace App\Support;

class CalculateWeightBasedCharge
{
    public static function run(
        float|int|string $totalWeight,
        string $shippingType,
        int|string $isd_fee,
        int|string $osd_fee,
        int|string $additional_isd_fee,
        int|string $additional_osd_fee,

    ): mixed {
        $shippingFee = 0;
        $actualWeight = max(0, intval(round($totalWeight)) - 1 ?? 0);

        if ($shippingType === 'ISD') {
            $shippingFee = $isd_fee + ($additional_isd_fee * $actualWeight);
        } else {
            $shippingFee = $osd_fee + ($additional_osd_fee * $actualWeight);
        }

        return $shippingFee;
    }
}
