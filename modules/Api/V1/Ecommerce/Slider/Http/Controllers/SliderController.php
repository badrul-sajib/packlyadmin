<?php

namespace Modules\Api\V1\Ecommerce\Slider\Http\Controllers;

use App\Actions\FetchPromotionProducts;
use App\Actions\FetchSlider;
use App\Http\Controllers\Controller;
use Modules\Api\V1\Ecommerce\Slider\Http\Resources\SliderPromotionResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SliderController extends Controller
{
    public function index(Request $request)
    {
        $sliders = (new FetchSlider)->handle();

        return success('Sliders showed successfully', $sliders);
    }

    public function promotionProducts(Request $request)
    {
        try {
            $sliderProducts = (new FetchPromotionProducts)->execute($request);

            return success('Slider promotion products showed successfully', new SliderPromotionResource($sliderProducts));
        } catch (ModelNotFoundException) {
            return failure('Slider not found', Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return failure('An error occurred while fetching promotion products', 500);
        }
    }
}
