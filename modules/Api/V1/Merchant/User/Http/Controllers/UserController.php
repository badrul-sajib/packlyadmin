<?php

namespace Modules\Api\V1\Merchant\User\Http\Controllers;

use App\Enums\UserRole;
use App\Services\ApiResponse;
use Modules\Api\V1\Merchant\User\Services\UserService;
use Modules\Api\V1\Merchant\User\Http\Requests\StoreUserRequest;
use Modules\Api\V1\Merchant\User\Http\Requests\UpdateUserRequest;
use Modules\Api\V1\Merchant\User\Http\Resources\UserResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Services\ErrorLogService;

class UserController extends Controller
{
    public function __construct(protected UserService $service)
    {
        $this->middleware('shop.permission:show-users')->only(['index', 'show']);
        $this->middleware('shop.permission:create-user')->only('store');
        $this->middleware('shop.permission:update-user')->only('update');
        $this->middleware('shop.permission:update-user-status')->only('updateStatus');
        $this->middleware('shop.permission:delete-user')->only('destroy');
    }

    private function resolvedMerchant(): ?\App\Models\Merchant\Merchant
    {
        return auth()->user()->merchant;
    }

    public function index(): JsonResponse
    {
        $merchant = $this->resolvedMerchant();

        if (!$merchant) {
            return ApiResponse::error('Merchant not found', 404);
        }

        try {
            $users = $this->service->getMerchantShopAdminsPaginated($merchant, 15);

            return ApiResponse::formatPagination('Users retrieved', $users);
        } catch (\Throwable $e) {
            return ApiResponse::error('Something went wrong', 500);
        }
    }

    public function store(StoreUserRequest $request): JsonResponse
    {
        $merchant = $this->resolvedMerchant();

        if (!$merchant) {
            return ApiResponse::error('Merchant not found', 404);
        }

        DB::beginTransaction();
        DB::connection('mysql_external')->beginTransaction();

        try {
            $user = $this->service->createShopAdmin(
                $merchant,
                $request->validated(),
                $request->input('roles', [])
            );

            DB::commit();
            DB::connection('mysql_external')->commit();

            return ApiResponse::successMessageForCreate(
                'User created',
                new UserResource($user->load('roles')),
                201
            );
        } catch (\Throwable $e) {
            DB::rollBack();
            DB::connection('mysql_external')->rollBack();

            return ApiResponse::error('Something went wrong', 500);
        }
    }

    public function show(int $id): JsonResponse
    {
        $merchant = $this->resolvedMerchant();

        if (!$merchant) {
            return ApiResponse::error('Merchant not found', 404);
        }

        try {
            $user = $this->service->findMerchantUserOrFail($merchant, $id);

            return ApiResponse::success('User retrieved', new UserResource($user));
        } catch (ModelNotFoundException $e) {
            return ApiResponse::error('User not found', 404);
        } catch (\Throwable $e) {
            return ApiResponse::error('Something went wrong', 500);
        }
    }

    public function update(UpdateUserRequest $request, int $id): JsonResponse
    {
        $merchant = $this->resolvedMerchant();

        if (!$merchant) {
            return ApiResponse::error('Merchant not found', 404);
        }

        DB::connection('mysql_external')->beginTransaction();

        try {
            $user = $this->service->updateMerchantUser(
                $merchant,
                $id,
                $request->validated(),
                $request->input('roles', null)
            );

            DB::connection('mysql_external')->commit();

            return ApiResponse::success('User updated successfully', new UserResource($user->load('roles')));
        } catch (ModelNotFoundException $e) {
            DB::connection('mysql_external')->rollBack();
            return ApiResponse::error('User not found', 404);
        } catch (\Throwable $e) {
            DB::connection('mysql_external')->rollBack();
            return ApiResponse::error('Something went wrong', 500);
        }
    }

    public function updateStatus(Request $request, int $id): JsonResponse
    {
        $merchant = $this->resolvedMerchant();

        if (!$merchant) {
            return ApiResponse::error('Merchant not found', 404);
        }

        $request->validate(['status' => 'required|boolean']);

        try {
            $user = $this->service->updateStatus(
                $merchant,
                $id,
                $request->boolean('status')
            );

            return ApiResponse::success('User status updated successfully', new UserResource($user));
        } catch (ModelNotFoundException $e) {
            return ApiResponse::error('User not found', 404);
        } catch (\Throwable $e) {
            ErrorLogService::log([
                'source'        => 'backend',
                'client_type'   => 'server',
                'user_id'       => null,
                'status_code'   => 500,
                'endpoint'      => request()->fullUrl(),
                'current_route' => request()->fullUrl(),
                'user_agent'    => request()->userAgent(),
                'environment'   => config('app.env'),
                'message'       => $e->getMessage(),
                'stack'         => $e->getTraceAsString(),
                'ip_address'    => request()->ip(),
                'file'          => $e->getFile(),
                'line'          => $e->getLine(),
            ]);

            return ApiResponse::error('Something went wrong', 500);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        $merchant = $this->resolvedMerchant();

        if (!$merchant) {
            return ApiResponse::error('Merchant not found', 404);
        }

        try {
            $this->service->deleteMerchantUser($merchant, $id);

            return ApiResponse::success('User deleted successfully');
        } catch (ModelNotFoundException $e) {
            return ApiResponse::error('User not found', 404);
        } catch (\Throwable $e) {
            return ApiResponse::error('Something went wrong', 500);
        }
    }
}
