<?php

namespace App\Http\Controllers\Admin\Report;

use App\Http\Controllers\Controller;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:revenue-report-list')->only('revenue');
    }

    /**
     * Display revenue dashboard with statistics
     */
    public function revenue(Request $request): View
    {
        if (!$request->filled('start_date') && !$request->filled('end_date')) {
            $request->merge([
                'start_date' => now()->subDays(7)->toDateString(),
                'end_date' => now()->toDateString(),
            ]);
        }

        $result = OrderService::getPaidOrders($request);

        $orders = $result['orders'];
        $stats = $result['stats'];
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        return view('Admin::report.revenue', compact(
            'orders',
            'stats',
            'startDate',
            'endDate'
        ));
    }
}
