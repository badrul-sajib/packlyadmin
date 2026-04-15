<?php

namespace App\Actions;

use App\Models\Slider\Slider;

class FetchSlider
{
    public function execute($request)
    {
        $status  = $request->status ?? '';
        $search  = $request->search ?? '';

        return Slider::with('media')
            ->when($search, function ($query) use ($search) {
                $query->where('title', 'like', "%{$search}%");
            })
            ->when($status, function ($query) use ($status) {
                return $query->where('status', $status);
            })->get();
    }

    public function handle()
    {
        $slider_type = request('slider_type') ?? '';

        return Slider::with('media')->where('status', 'active')
            ->when($slider_type, function ($query) use ($slider_type) {
                $query->where('slider_type', $slider_type);
            })
            ->get()->groupBy('slider_type')->map(function ($slider) {
                return [
                    'slider_type' => $slider[0]->label ?? '',
                    'sliders'     => $slider->map(function ($item) {
                        return [
                            'id'                    => $item->id,
                            'title'                 => $item->title,
                            'sub_title'             => $item->sub_title,
                            'link'                  => $item->link,
                            'status'                => $item->status,
                            'full_image'            => $item->full_image,
                            'desktop_label_banner'  => $item->desktop_label_banner,
                            'mobile_label_banner'   => $item->mobile_label_banner,
                            'small_image'           => $item->small_image,
                            'label_name'            => $item->label_name,
                            'label_slug'            => $item->label_slug,
                        ];
                    }),
                ];
            });
    }
}
