<?php

namespace Modules\Api\V1\Merchant\Issue\Http\Controllers;

use App\Enums\MerchantIssueStatus;
use App\Http\Controllers\Controller;
use Modules\Api\V1\Merchant\Issue\Http\Requests\MerchantIssueRequest;
use Modules\Api\V1\Merchant\Issue\Http\Resources\MerchantIssueResource;
use App\Models\Merchant\MerchantIssue;
use App\Models\Merchant\MerchantIssueType;
use App\Services\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IssueController extends Controller
{
    public function __construct()
    {
        $this->middleware('shop.permission:show-issues')->only('index');
        $this->middleware('shop.permission:create-issue')->only('store');
    }
    public function index(Request $request): JsonResponse
    {
        $merchantId = auth()->user()->merchant->id;

        $issues = MerchantIssue::with('type')
            ->where('merchant_id', $merchantId)
            ->latest()
            ->paginate();

        $data = MerchantIssueResource::collection($issues);

        return resourceFormatPagination('Issues retrieved successfully', $data, $issues, Response::HTTP_OK);
    }

    public function store(MerchantIssueRequest $request): JsonResponse
    {
        $merchantId = auth()->user()->merchant->id;

        // ensure issue type is active
        $type = MerchantIssueType::query()->where('id', $request->merchant_issue_type_id)->where('is_active', true)->first();
        if (! $type) {
            return ApiResponse::failure('Invalid or inactive issue type', Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $issueExists = MerchantIssue::where('merchant_id', $merchantId)
            ->where('merchant_issue_type_id', $type->id)
            ->where('message', $request->input('message'))
            ->where('status', MerchantIssueStatus::Pending)
            ->first();

        if ($issueExists) {
            return ApiResponse::failure('An identical pending issue already exists', Response::HTTP_CONFLICT);
        }

        $issue = MerchantIssue::create([
            'merchant_id'            => $merchantId,
            'merchant_issue_type_id' => $type->id,
            'message'                => $request->input('message'),
        ]);

        // Attach files if provided
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $issue->addMedia($file, 'attachments');
            }
        }

        return ApiResponse::success(
            'Issue submitted successfully',
            new MerchantIssueResource($issue->load('type')),
            Response::HTTP_CREATED
        );
    }
}
