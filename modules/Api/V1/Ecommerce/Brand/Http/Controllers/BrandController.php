<?php

namespace Modules\Api\V1\Ecommerce\Brand\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Brand\Brand;
use Exception;
use Symfony\Component\HttpFoundation\Response;

class BrandController extends Controller
{
    public static function index(): mixed
    {
        try {
            $brands = Brand::get()->map(function ($brand) {
                return [
                    'id'            => $brand->id,
                    'name'          => $brand->name,
                    'slug'          => $brand->slug,
                    'image'         => $brand->image,
                ];
            });

            return success('Fetched all brands', $brands);
        } catch (Exception $e) {
            return failure('Failed to fetch brands', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
