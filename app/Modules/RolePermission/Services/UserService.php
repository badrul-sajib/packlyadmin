<?php

namespace App\Modules\RolePermission\Services;

use App\Enums\UserRole;
use App\Models\User\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class UserService
{
    public function getAdmins($request)
    {
        $perPage = $request->input('perPage', 10);
        $page    = $request->input('page', 1);
        $search  = $request->input('search', '');
        $role    = UserRole::ADMIN->value;

        return User::where('role', $role)
            ->with('permissions')
            ->when($search, function ($query) use ($search) {
                $query->whereAny(['name', 'email', 'phone'], 'like', "%{$search}%");
            })
            ->paginate($perPage, ['*'], 'page', $page)->withQueryString();
    }

    public function createAdmin($data)
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'phone'    => $data['phone'],
            'password' => Hash::make($data['password']),
            'role'     => UserRole::ADMIN->value,
        ]);

        if (! empty($data['avatar'])) {
            $user->avatar = $data['avatar'];
        }

        if ($user && ! empty($data['role_id'])) {
            $user->assignRoleToUser(intval($data['role_id']));
        }

        if (isset($data['permissions'])) {
            $permissions = Permission::query()
                ->where('guard_name', 'admin')
                ->whereIn('name', (array) $data['permissions'])
                ->pluck('name')
                ->toArray();

            $user->syncPermissions($permissions);
        } else {
            $user->permissions()->detach();
        }

        return $user->save();
    }

    public function getAdminById($id)
    {
        return User::find($id);
    }

    public function updateAdmin($data, $id)
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $user = $this->getAdminById($id);
        $user->update([
            'name'  => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
        ]);

        if (! empty($data['avatar'])) {
            $user->avatar = $data['avatar'];
        }

        $role = intval($data['role_id']);
        $user->syncRoles($role);

        if (isset($data['permissions'])) {
            $permissions = Permission::query()
                ->where('guard_name', 'admin')
                ->whereIn('name', (array) $data['permissions'])
                ->pluck('name')
                ->toArray();

            $user->syncPermissions($permissions);
        } else {
            $user->permissions()->detach();
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
        Cache::forget('user_permissions'.$user->id);

        return $user->save();
    }

    public function deleteAdmin($id)
    {
        $user = $this->getAdminById($id);

        return $user->delete();
    }
}
