<?php

namespace Modules\Api\V1\Ecommerce\EPage\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\EPageService;

class EPageController extends Controller
{
    public function __construct(private readonly EPageService $ePageService) {}

    public function index()
    {
        $ePages = $this->ePageService->getEPagesMenu();

        return success('Success', $ePages);
    }

    public function show(string $slug)
    {
        $ePages = $this->ePageService->getBySlug($slug);

        return success('Success', $ePages);
    }
}
