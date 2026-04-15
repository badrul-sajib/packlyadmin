<?php

namespace App\Http\Controllers\Admin\Merchant;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Services\Merchant\MerchantReportService;
use App\Http\Requests\Admin\CreateMerchantReportRequest;

class MerchantReportController extends Controller
{
    public function __construct(private readonly MerchantReportService $merchantReportService)
    {
        $this->middleware(('permission:merchant-inactive'))->only('create');
    }

    public function store(CreateMerchantReportRequest $request)
    {
        try {
            $this->merchantReportService->store($request->validated());

            return success('Merchant report created successfully');
        } catch (Exception $e) {
            Log::error($e->getMessage());

            return failure('Something went wrong', 500);
        }
    }

    public function create(Request $request)
    {
        if (! $request->id) {
            abort(404);
        }

        return view('Admin::merchant.report-create', ['id' => $request->id]);

    }

    public function show(int $id)
    {
        $report = $this->merchantReportService->show($id);

        return view('Admin::merchant.report-show', compact('report'));
    }
}
