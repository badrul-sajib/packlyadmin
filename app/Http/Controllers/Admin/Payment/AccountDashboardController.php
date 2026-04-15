<?php

namespace App\Http\Controllers\Admin\Payment;

use App\Http\Controllers\Controller;
use App\Services\Admin\DashboardService;
use Illuminate\Http\Request;

class AccountDashboardController extends Controller
{
    protected DashboardService $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
        $this->middleware('permission:account-dashboard-list')->only('index');
    }

    public function index(Request $request)
    {
        $filter = strtolower((string) $request->get('filter', ''));

        $hasExplicitDate = $request->filled('start_date') || $request->filled('end_date');
        if ($filter === 'all') {
            $dateRange = [
                'startDate' => null,
                'endDate'   => null,
            ];
            $startDateParam = null;
            $endDateParam   = null;
        } else {
            $startDateParam = $request->start_date ?? now()->format('Y-m-d');
            $endDateParam   = $request->end_date   ?? now()->format('Y-m-d');
            $dateRange = $this->dashboardService->getDateRange($startDateParam, $endDateParam);
        }

        $statistics = $this->dashboardService->getAccountStatistics($dateRange['startDate'], $dateRange['endDate']);

        return view('Admin::account_dashboard', [
            'startDate'      => $dateRange['startDate'],
            'endDate'        => $dateRange['endDate'],
            'startDateParam' => $startDateParam,
            'endDateParam'   => $endDateParam,
            'filter'         => $filter,
            'statistics' => $statistics,
        ]);
    }
}
