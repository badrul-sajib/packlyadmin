<?php

namespace App\Actions;

use App\Models\Slider\Slider;

class FetchPromotionProducts
{
    public function execute($data)
    {
        $slug = $data->get('slug');

        return Slider::with([
                'slider_products' => function ($query)  {
                    $query->baseShopQuery();
                },
            ])
            ->whereNotNull('label_slug')
            ->where('status', 'active')
            ->where('label_slug', $slug)
            ->firstOrFail();
    }
}
