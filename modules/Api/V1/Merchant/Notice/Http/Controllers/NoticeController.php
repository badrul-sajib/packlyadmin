<?php

namespace Modules\Api\V1\Merchant\Notice\Http\Controllers;

use App\Enums\MerchantReportStatus;
use App\Http\Controllers\Controller;
use App\Jobs\PushNotification;
use App\Models\Merchant\MerchantReport;
use App\Services\ApiResponse;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class NoticeController extends Controller
{

    public function __construct()
    {
        $this->middleware('shop.permission:show-merchant-notice')->only('index', 'show');
        $this->middleware('shop.permission:update-merchant-notice')->only('update');
    }

    public function index(): JsonResponse
    {
        $reports = MerchantReport::where('merchant_id', auth()->user()->merchant->id)->latest()->paginate();

        $formattedReports = $reports->getCollection()->map(function ($report) {
            return [
                'id'         => $report->id,
                'title'      => \Str::limit(strip_tags($report->report_details), 5, '...'),
                'status'     => $report->status,
                'created_at' => $report->created_at->format('Y/m/d H:i'),
                'updated_at' => $report->updated_at->format('Y/m/d H:i'),
            ];
        });

        $paginatedReports = new LengthAwarePaginator($formattedReports, $reports->total(), $reports->perPage(), $reports->currentPage(), ['path' => $reports->path()]);

        return ApiResponse::formatPagination('All Reports retrieve successfully', $paginatedReports, Response::HTTP_OK);
    }

    public function show(int $id): JsonResponse
    {
        $report = MerchantReport::find($id);

        if (! $report) {
            return ApiResponse::error('Report not found', Response::HTTP_NOT_FOUND);
        }

        $report = [
            'id'             => $report->id,
            'report_details' => $report->report_details,
            'appeal'         => $report->appeal,
            'status'         => $report->status,
            'created_at'     => $report->created_at->format('Y/m/d H:i'),
        ];

        return ApiResponse::success('Report retrieved successfully', $report, Response::HTTP_OK);
    }

    public function update(int $id, Request $request): JsonResponse
    {
        if (empty($request->appeal)) {
            return ApiResponse::error('Please enter an appeal', Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $merchantReport = MerchantReport::find($id);
        if (! $merchantReport) {
            return ApiResponse::error('Report not found', Response::HTTP_NOT_FOUND);
        }

        if ($merchantReport->status == MerchantReportStatus::Resolved->value) {
            return ApiResponse::error('Report already resolved', Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $merchantReport->status = MerchantReportStatus::Resolved->value;
        $merchantReport->appeal = $request->appeal;

        $merchantReport->save();

        $notificationMessage = 'Merchant submit appeal from ' . auth()->user()->name . '.';

        try {
            PushNotification::dispatch([
                'title'      => 'Merchant submit appeal from',
                'message'    => $notificationMessage,
                'type'       => 'info',
                'action_url' => "/merchants/$id",
            ]);
        } catch (Exception $e) {
            Log::error($e->getMessage());
        }

        return ApiResponse::success('Report updated successfully', $merchantReport, Response::HTTP_OK);
    }
}
