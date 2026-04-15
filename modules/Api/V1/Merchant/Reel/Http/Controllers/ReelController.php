<?php

namespace Modules\Api\V1\Merchant\Reel\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Jobs\PushNotification;
use App\Services\ApiResponse;
use App\Services\Merchant\ReelService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Modules\Api\V1\Merchant\Reel\Http\Requests\ReelRequest;
use Modules\Api\V1\Merchant\Reel\Http\Resources\ReelResource;
use Symfony\Component\HttpFoundation\Response;

class ReelController extends Controller
{
    protected ReelService $reelService;

    public function __construct(ReelService $reelService)
    {
        $this->reelService = $reelService;
        $this->middleware('shop.permission:show-reels')->only('index', 'show');
        $this->middleware('shop.permission:create-reel')->only('store');
        $this->middleware('shop.permission:update-reel')->only('update');
        $this->middleware('shop.permission:delete-reel')->only('destroy');
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $reels = $this->reelService->getAllReels($request);

            return ApiResponse::formatPagination('Reels retrieved successfully', ReelResource::collection($reels), Response::HTTP_OK);
        } catch (Exception $e) {
            return ApiResponse::error('Something went wrong', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function store(ReelRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();

            $reel = $this->reelService->createReel($data);

            if ($request->hasFile('image')) {
                $reel->image = $request->file('image');
                $reel->save();
            }

            if ($request->hasFile('video')) {
                $reel->video = $request->file('video');
                $reel->save();
            }

            if ($request->hasFile('thumbnail_image')) {
                $reel->thumbnail_image = $request->file('thumbnail_image');
                $reel->save();
            }

            $notificationMessage = 'New reel create request by '.auth()->user()->name.'.';

            try {
                PushNotification::dispatch([
                    'title' => 'New Reel Create Request',
                    'message' => $notificationMessage,
                    'type' => 'info',
                    'action_url' => '/reels',
                ]);
            } catch (Exception $e) {
                Log::error($e->getMessage());
            }

            return ApiResponse::successMessageForCreate(
                'Reel created successfully, Please wait for admin approval.',
                $reel,
                Response::HTTP_CREATED
            );
        } catch (Exception $e) {
            return ApiResponse::error('Something went wrong', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show(int $id)
    {
        try {
            $reel = $this->reelService->getReelById($id);
            if (! $reel) {
                return ApiResponse::failure('Reel not found', Response::HTTP_NOT_FOUND);
            }

            return ApiResponse::success('Reel retrieved successfully', $reel, Response::HTTP_OK);
        } catch (Exception $e) {
            return ApiResponse::error('Something went wrong', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(ReelRequest $request, int $id): JsonResponse
    {
        try {
            $data = $request->validated();

            $reel = $this->reelService->updateReel($id, $data);

            if (! $reel) {
                return ApiResponse::failure('Reel not found', Response::HTTP_NOT_FOUND);
            }

            if ($request->hasFile('image')) {
                $reel->image = $request->file('image');
                $reel->save();
            }

            return ApiResponse::success('Reel updated successfully', $reel, Response::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            return ApiResponse::error('Something went wrong', Response::HTTP_NOT_FOUND);
        } catch (Exception $e) {
            return ApiResponse::error('Something went wrong', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $deleted = $this->reelService->deleteReel($id);
            if (! $deleted) {
                return ApiResponse::failure('Reel not found', Response::HTTP_NOT_FOUND);
            }

            return ApiResponse::success('Reel deleted successfully', [], Response::HTTP_OK);
        } catch (Exception $e) {
            return ApiResponse::error('Something went wrong', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
