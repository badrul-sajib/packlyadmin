<?php

namespace Modules\Api\V1\Merchant\CategoryRequest\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Jobs\PushNotification;
use App\Models\Category\CategoryCreateRequest;
use App\Services\ApiResponse;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Modules\Api\V1\Merchant\CategoryRequest\Http\Requests\CategoryRequestRequest;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class CategoryRequestController extends Controller
{
    public function __construct()
    {
        $this->middleware('shop.permission:show-category-request')->only('index');
        $this->middleware('shop.permission:create-category-request')->only('store');
    }
    /*
     * Retrieves a list of category requests.
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->input('per_page', 10);

        try {
            $merchantId = Auth::user()->merchant?->id;

            $categoryRequests = CategoryCreateRequest::where('merchant_id', $merchantId)
                ->orderBy('created_at', 'desc')
                ->paginate($perPage);

            $categoryRequests = new LengthAwarePaginator(
                $categoryRequests,
                $categoryRequests->total(),
                $categoryRequests->perPage(),
                $categoryRequests->currentPage(),
                ['path' => $categoryRequests->path()]
            );

            return ApiResponse::formatPagination('Category create requests retrieved successfully.', $categoryRequests, Response::HTTP_OK);
        } catch (Exception $e) {
            return ApiResponse::failure('Category create requests not found.', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /*
     * Submits a new category creation request.
     */
    /**
     * @throws Throwable
     */
    public function store(CategoryRequestRequest $request): JsonResponse
    {
        DB::beginTransaction();

        try {
            $merchantId = Auth::user()->merchant?->id;

            // Validate request limit
            if (CategoryCreateRequest::where('merchant_id', $merchantId)->count() >= 10) {
                return ApiResponse::failure("You've reached the maximum limit of 10 requests.", Response::HTTP_FORBIDDEN);
            }

            $validated = $request->validated();

            $categories = json_decode($validated['categories'], true);

            // Validate JSON structure
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw ValidationException::withMessages([
                    'categories' => 'Invalid JSON format',
                ]);
            }

            // Create the request
            $request = CategoryCreateRequest::create([
                'merchant_id' => $merchantId,
                'note'        => $validated['note'],
                'data'        => $categories,
                'status'      => 1,
            ]);

            $notificationMessage = 'New category create request by ' . auth()->user()->name . '.';

            DB::commit();

            try {
                PushNotification::dispatch([
                    'title'      => 'New Category Create Request',
                    'message'    => $notificationMessage,
                    'type'       => 'info',
                    'action_url' => '/category-create-requests/' . $request->id,
                ]);
            } catch (Throwable $th) {
                Log::error($th->getMessage());
            }

            return ApiResponse::success('Request submitted successfully', $request, Response::HTTP_CREATED);
        } catch (ValidationException $e) {
            DB::rollBack();

            return ApiResponse::validationError('There were validation errors.', $e->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (Exception $e) {
            DB::rollBack();

            return ApiResponse::failure('Request not submitted.', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
