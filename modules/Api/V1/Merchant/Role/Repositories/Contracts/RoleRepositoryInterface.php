<?php

namespace Modules\Api\V1\Merchant\Role\Repositories\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface RoleRepositoryInterface
{
    public function getPaginatedForCurrentMerchant(int $perPage = 15): LengthAwarePaginator;

    public function findByIdForCurrentMerchant(int $id): Model;

    public function createWithPermissions(array $data, array $permissionIds = []): Model;

    public function updateWithPermissions(int $id, array $data, array $permissionIds = []): Model;

    public function delete(int $id): bool;

    public function getAllPermissionsGroupedPermissions(): Collection;

    public function checkRoleNameExistsForMerchant(string $name, ?int $excludeId = null): bool;
}
