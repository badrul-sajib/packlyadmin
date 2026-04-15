<?php

namespace Modules\Api\V1\Merchant\Role\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Spatie\Permission\Models\Permission;

class RoleDetailResource extends JsonResource
{
    public function toArray($request): array
    {
        $rolePermissionIds = $this->permissions->pluck('id')->toArray();

        // Fetch all api permissions to build the complete list with enabled flags
        $permissions = Permission::select('id', 'name', 'group_name')
            ->where('guard_name', 'api')
            ->orderBy('group_name')
            ->orderBy('name')
            ->get()
            ->map(function ($permission) use ($rolePermissionIds) {
                $permission->enabled = in_array($permission->id, $rolePermissionIds);
                return $permission;
            })
            ->groupBy('group_name')
            ->map(function ($group) {
                return $group->values();
            });

        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'description' => $this->description ?? null,
            'permissions' => $permissions,
        ];
    }
}
