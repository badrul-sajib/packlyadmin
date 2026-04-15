<?php

namespace App\Modules\RolePermission\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User\User;
use App\Modules\RolePermission\Requests\RoleStoreRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cache;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:role-list')->only('index');
        $this->middleware('permission:role-create')->only(['create', 'store']);
        $this->middleware('permission:role-update')->only(['edit', 'update']);
        $this->middleware('permission:role-delete')->only('destroy');
    }

    public function index()
    {
        $roles = Role::with('permissions')->where('guard_name','admin')->get();

        return view('RolePermission::roles.index', compact('roles'));
    }

    public function create()
    {
        $groupedPermissions = Permission::select('group_name', 'id', 'name')
            ->where('guard_name','admin')
            ->orderBy('group_name')->get()->groupBy('group_name');

        return view('RolePermission::roles.create', compact('groupedPermissions'));
    }

    public function store(RoleStoreRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $role = Role::create($data);

        if ($request->permissions) {
            $role->syncPermissions($request->permissions);
        }

        return redirect()->route('roles.index')->with('success', 'Role created successfully.');
    }

    public function edit(Role $role)
    {
        $groupedPermissions = Permission::select('group_name', 'id', 'name')
            ->where('guard_name','admin')
            ->orderBy('group_name')->get()->groupBy('group_name');

        return view('RolePermission::roles.edit', compact('role', 'groupedPermissions'));
    }

    public function update(RoleStoreRequest $request, Role $role): RedirectResponse
    {
        $data = $request->validated();

        $role->update($data);
        if ($request->permissions) {
            $role->syncPermissions($request->permissions);
        }

        // Reset permissions for all users with this role
        $users = $role->users()->take(User::$MAX_USERS_TO_UPDATE_PERMISSIONS)->get();
        foreach ($users as $user) {
            Cache::forget('user_permissions'.$user->id);
        }

        return redirect()->route('roles.index')->with('success', 'Role updated successfully.');
    }

    public function destroy(Role $role): RedirectResponse|JsonResponse
    {
        $hasAssignedUsers = $role->users()->exists();

        if ($hasAssignedUsers) {
            $message = 'This role cannot be deleted as it is currently assigned to active users.';

            if (request()->ajax() || request()->expectsJson()) {
                return response()->json(['message' => $message], 422);
            }

            return redirect()->route('roles.index')->withErrors(['role' => $message]);
        }

        $role->delete();

        if (request()->ajax() || request()->expectsJson()) {
            return response()->json(['message' => 'Role deleted successfully.']);
        }

        return redirect()->route('roles.index')->with('success', 'Role deleted successfully.');
    }
}
