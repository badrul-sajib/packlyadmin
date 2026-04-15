<?php

namespace App\Services;

use App\Models\Slider\Slider;

class SliderService
{
    /**
     * Store a new slider.
     */
    public function store(array $data): Slider
    {

        $slider = Slider::create([
            'title'       => $data['title'],
            'sub_title'   => $data['sub_title'],
            'link'        => $data['link'],
            'label_name'  => $data['label_name'],
            'slider_type' => $data['slider_type'],
            'status'      => $data['status'],
        ]);
        $slider->full_image           = $data['full_image'];
        $slider->small_image          = $data['small_image'];
        $slider->desktop_label_banner = $data['desktop_label_banner'];
        $slider->mobile_label_banner  = $data['mobile_label_banner'];

        if (isset($data['product_ids'])) {
            $slider->slider_products()->sync($data['product_ids']);
        }

        return $slider;
    }

    /**
     * Update an existing slider.
     */
    public function update(Slider $slider, array $data): Slider
    {
        $productIds = $data['product_ids'] ?? [];
        unset($data['product_ids']);

        $slider->update($data);

        if (filled($productIds)) {
            $slider->slider_products()->sync($productIds);
        }else {
            $slider->slider_products()->detach();
        }
        
        return $slider;
    }

    /**
     * Delete a slider.
     */
    public function delete(Slider $slider): bool
    {
        return $slider->delete();
    }
}
