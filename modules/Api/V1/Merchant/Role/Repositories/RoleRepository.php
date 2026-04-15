<?php

namespace Modules\Api\V1\Merchant\Role\Repositories;

use Exception;
use Modules\Api\V1\Merchant\Role\Repositories\Contracts\RoleRepositoryInterface;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;


class RoleRepository implements RoleRepositoryInterface
{
    public function __construct(protected Role $model) {}

    protected function query()
    {
        return auth()->user()->merchant->roles();
    }

    public function getPaginatedForCurrentMerchant(int $perPage = 15): LengthAwarePaginator
    {
            return $this->query()
                ->with('permissions')
                ->selectRaw('roles.*, (SELECT COUNT(*) FROM model_has_roles WHERE model_has_roles.role_id = roles.id AND model_has_roles.model_type = ?) as users_count', [
                    \App\Models\User\User::class
                ])
                ->paginate($perPage);
    }

    public function findByIdForCurrentMerchant(int $id): Model
    {
        $role = $this->query()->with('permissions')->find($id);

        if (!$role) {
            throw new Exception('Role not found or does not belong to your merchant.');
        }

        return $role;
    }

    public function checkRoleNameExistsForMerchant(string $name, ?int $excludeId = null): bool
    {
        $query = $this->query()->where('name', $name);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }

    public function createWithPermissions(array $data, array $permissionIds = []): Model
    {
        return DB::transaction(function () use ($data, $permissionIds) {
            $role = $this->model->newInstance([
                'name'        => $data['name'],
                'guard_name'  => 'api',
                'description' => $data['description'] ?? null,
            ]);
            $role->shop_id = auth()->user()->merchant->id;
            $role->save();

            if (!empty($permissionIds)) {
                $permissions = Permission::where('guard_name', 'api')
                    ->whereIn('id', $permissionIds)
                    ->pluck('id');

                if ($permissions->isEmpty()) {
                    throw new \InvalidArgumentException('Invalid permissions selected.');
                }

                $role->syncPermissions($permissions);
            }

            return $role->load('permissions');
        });
    }

    public function updateWithPermissions(int $id, array $data, array $permissionIds = []): Model
    {
        return DB::transaction(function () use ($id, $data, $permissionIds) {
            $role = $this->findByIdForCurrentMerchant($id);

            // Ensure description is preserved when provided (nullable allowed)
            if (array_key_exists('description', $data)) {
                $role->description = $data['description'];
                unset($data['description']);
            }

            $role->update($data);

            if (isset($permissionIds)) {
                if (empty($permissionIds)) {
                    // If existing logic requires clearing permissions on empty array
                    $role->syncPermissions([]);
                } else {
                    $permissions = Permission::where('guard_name', 'api')
                        ->whereIn('id', $permissionIds)
                        ->pluck('id');

                    // Strict check to ensure valid permissions
                    if ($permissions->isEmpty()) {
                        throw new \InvalidArgumentException('Invalid permissions selected.');
                    }

                    $role->syncPermissions($permissions);
                }
            }

            return $role->load('permissions');
        });
    }

    public function delete(int $id): bool
    {
        $role = $this->findByIdForCurrentMerchant($id);
        return $role->delete();
    }

    public function getAllPermissionsGroupedPermissions(): Collection
    {
        return Permission::where('guard_name', 'api')
            ->select('id', 'name', 'group_name')
            ->orderBy('group_name')
            ->orderBy('id', 'asc')
            ->orderBy('name')
            ->get()
            ->groupBy('group_name');
    }
}
