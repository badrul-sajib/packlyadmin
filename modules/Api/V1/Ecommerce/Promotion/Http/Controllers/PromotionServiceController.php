<?php

namespace Modules\Api\V1\Ecommerce\Promotion\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\PromotionServiceService;

class PromotionServiceController extends Controller
{
    public function __construct(private readonly PromotionServiceService $promotionServiceService) {}

    public function index()
    {
        $promotionServices = $this->promotionServiceService->getPromotionServicesMenu();

        return success('Success', $promotionServices);
    }

    public function show(string $slug)
    {
        $promotionServices = $this->promotionServiceService->getBySlug($slug);

        return success('Success', $promotionServices);
    }
}
