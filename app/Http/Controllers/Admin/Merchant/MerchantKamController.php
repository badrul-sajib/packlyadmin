<?php

namespace App\Http\Controllers\Admin\Merchant;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Merchant\MerchantKamService;

class MerchantKamController extends Controller
{
    public function __construct(
        protected readonly MerchantKamService $merchantKamService
    ){}

    public function orders(Request $request)
    {
        $orders = $this->merchantKamService->orders($request);

        if ($request->ajax()) {
            return view('components.orders.merchant_table', ['entity' => $orders])->render();
        }

        $startDate = $request->input('start_date');
        $endDate   = $request->input('end_date');

        return view('Admin::kam.orders', compact('orders', 'startDate', 'endDate'));
    }

    public function merchants(Request $request)
    {
        $merchants = $this->merchantKamService->merchants($request);

        if ($request->ajax()) {
            return view('components.merchant.table', ['entity' => $merchants])->render();
        }

        return view('Admin::kam.merchants', compact('merchants'));
    }
    public function products()
    {
        $products = $this->merchantKamService->products();
    }
}
