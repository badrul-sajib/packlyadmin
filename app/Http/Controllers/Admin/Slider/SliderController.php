<?php

namespace App\Http\Controllers\Admin\Slider;

use App\Actions\FetchSlider;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SliderRequest;
use App\Models\Slider\Slider;
use App\Services\SliderService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class SliderController extends Controller
{
    public function __construct(private readonly SliderService $sliderService) {}

    /**
     * Display a listing of the sliders.
     *
     * @throws Throwable
     */
    public function index(Request $request)
    {
        $sliders = (new FetchSlider)->execute($request);
        if ($request->ajax()) {
            return view('components.slider.table', ['entity' => $sliders])->render();
        }

        return view('Admin::sliders.index', compact('sliders'));
    }

    /**
     * Show the form for creating a new slider.
     */
    public function create(): View
    {
        $sliderProducts = [];

        return view('Admin::sliders.create', compact('sliderProducts'));
    }

    /**
     * Store a newly created slider in storage.
     */
    public function store(SliderRequest $request): JsonResponse
    {
        $this->sliderService->store($request->validated());

        return response()->json(['success' => 'Slider created successfully!']);
    }

    /**
     * Show the form for editing the specified slider.
     */
    public function edit(Slider $slider): View
    {
        $sliderProducts = $slider->slider_products;

        return view('Admin::sliders.edit', compact('slider', 'sliderProducts'));
    }

    /**
     * Update the specified slider in storage.
     */
    public function update(SliderRequest $request, Slider $slider): JsonResponse
    {
        $this->sliderService->update($slider, $request->validated());

        return response()->json(['success' => 'Slider updated successfully!']);
    }

    /**
     * Remove the specified slider from storage.
     */
    public function destroy(Slider $slider): JsonResponse
    {
        $this->sliderService->delete($slider);

        return response()->json(['success' => 'Slider deleted successfully!']);
    }
}
