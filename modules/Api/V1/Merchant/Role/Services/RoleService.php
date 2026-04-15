<?php

namespace Modules\Api\V1\Merchant\Role\Services;

use Exception;
use Modules\Api\V1\Merchant\Role\Repositories\Contracts\RoleRepositoryInterface;

class RoleService
{
    public function __construct(protected RoleRepositoryInterface $repository) {}

    public function paginate(int $perPage = 15)
    {
        return $this->repository->getPaginatedForCurrentMerchant($perPage);
    }

    public function find(int $id)
    {
        return $this->repository->findByIdForCurrentMerchant($id);
    }

    public function create(array $data)
    {
        if ($this->repository->checkRoleNameExistsForMerchant($data['name'])) {
            throw new Exception('Role name already exists');
        }

        return $this->repository->createWithPermissions($data, $data['permissions'] ?? []);
    }

    public function update(int $id, array $data)
    {
        if (isset($data['name']) && $this->repository->checkRoleNameExistsForMerchant($data['name'], $id)) {
            throw new Exception('The role name has already been taken');
        }

        $permissionIds = $data['permissions'] ?? null;

        return $this->repository->updateWithPermissions($id, $data, $permissionIds);
    }

    public function delete(int $id): void
    {
        $this->repository->delete($id);
    }

    public function getPermissions()
    {
        return $this->repository->getAllPermissionsGroupedPermissions();
    }
}
