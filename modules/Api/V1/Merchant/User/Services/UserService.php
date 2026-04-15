<?php

namespace Modules\Api\V1\Merchant\User\Services;

use App\Enums\UserRole;
use App\Models\User\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Modules\Api\V1\Merchant\User\Repositories\Contracts\UserRepositoryInterface;

class UserService
{
    public function __construct(protected UserRepositoryInterface $repository) {}

    public function getMerchantShopAdminsPaginated($merchant, int $perPage = 15)
    {
        return $merchant->users()
            ->where('role', UserRole::SHOP_ADMIN->value)
            ->with('roles:id,name')
            ->paginate($perPage);
    }

    public function findMerchantUserOrFail($merchant, int $id): User
    {
        $user = $merchant->users()->where('users.id', $id)->first();

        if (!$user) {
            throw new ModelNotFoundException('User not found or does not belong to your merchant.', 404);
        }

        return $user->load('roles');
    }

    public function createShopAdmin($merchant, array $data, array $roles = []): User
    {
        $data['password'] = Hash::make($data['password']);
        $data['role']     = UserRole::SHOP_ADMIN->value;
        unset($data['roles']);

        $user = $this->repository->create($data);

        // Attach to merchant (pivot table
        $merchant->users()->attach($user->id);

        // Sync Spatie roles if provided
        if (!empty($roles)) {
            $user->guard_name = 'api';
            $user->syncRoles($roles);
        }

        return $user->load('roles');
    }

    public function updateMerchantUser($merchant, int $id, array $data, ?array $roles = null): User
    {
        $user = $this->findMerchantUserOrFail($merchant, $id);

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }
        unset($data['roles'], $data['password_confirmation']);

        $user = $this->repository->update($id, $data);

        if ($roles !== null) {
            $user->guard_name = 'api';
            $user->syncRoles($roles);
        }

        return $user->refresh()->load('roles');
    }

    public function updateStatus($merchant, int $id, bool $status): User
    {
        $user = $this->findMerchantUserOrFail($merchant, $id);

        $this->repository->update($id, ['status' => $status ? '1' : '0']);

        return $user->refresh();
    }

    public function deleteMerchantUser($merchant, int $id): void
    {
        $user = $this->findMerchantUserOrFail($merchant, $id);

        $user->delete();

        // Detach from merchant
        $merchant->users()->detach($id);
    }
}
