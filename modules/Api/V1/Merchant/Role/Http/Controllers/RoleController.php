<?php

namespace Modules\Api\V1\Merchant\Role\Http\Controllers;

use App\Services\ApiResponse;
use Modules\Api\V1\Merchant\Role\Services\RoleService;
use Modules\Api\V1\Merchant\Role\Http\Requests\StoreRoleRequest;
use Modules\Api\V1\Merchant\Role\Http\Requests\UpdateRoleRequest;
use Modules\Api\V1\Merchant\Role\Http\Resources\RoleResource;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    public function __construct(
        protected RoleService $service
    ) {
        $this->middleware('shop.permission:show-roles')->only('index', 'show', 'permissions');
        $this->middleware('shop.permission:create-role')->only('store');
        $this->middleware('shop.permission:update-role')->only('update');
        $this->middleware('shop.permission:delete-role')->only('destroy');
    }

    public function index(): JsonResponse
    {
        try {
            $data = $this->service->paginate(15);
            return ApiResponse::formatPagination('Roles retrieved successfully', $data);
        } catch (\Throwable $e) {
            return ApiResponse::error('Something went wrong');
        }
    }

    public function store(StoreRoleRequest $request): JsonResponse
    {
        try {
            $role = $this->service->create($request->validated());
            $role->load('permissions');
            return ApiResponse::successMessageForCreate(
                'Role created successfully',
                new RoleResource($role),
                metadata: ['permissions' => $role->permissions],
                code: 201
            );
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), 422);
        } catch (\Throwable $e) {
            return ApiResponse::error('Something went wrong');
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $role = $this->service->find($id);
            return ApiResponse::success(
                'Role info',
                new \Modules\Api\V1\Merchant\Role\Http\Resources\RoleDetailResource($role)
            );
        } catch (\Throwable $e) {
            return ApiResponse::error('Role not found', 404);
        }
    }

    public function update(UpdateRoleRequest $request, int $id): JsonResponse
    {
        try {
            $role = $this->service->update($id, $request->validated());
            $role->load('permissions');
            return ApiResponse::success(
                'Role updated successfully',
                new RoleResource($role)
            );
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), 422);
        } catch (\Throwable $e) {
            return ApiResponse::error('Something went wrong');
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $this->service->delete($id);
            return ApiResponse::success('Role deleted successfully');
        } catch (\Throwable $e) {
            return ApiResponse::error('Something went wrong');
        }
    }

    public function permissions(): JsonResponse
    {
        $permissions = $this->service->getPermissions();
        return ApiResponse::success('Permissions retrieved', $permissions);
    }

    public function updateStatus(int $id): JsonResponse
    {
        try {
            $role = $this->service->find($id);
            $role->update(['status' => !$role->status]);
            return ApiResponse::success(
                'Role status updated successfully',
                new RoleResource($role)
            );
        } catch (\Throwable $e) {
            return ApiResponse::error('Role not found', 404);
        }
    }
}
