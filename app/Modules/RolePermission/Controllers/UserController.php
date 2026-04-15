<?php

namespace App\Modules\RolePermission\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User\User;
use App\Modules\RolePermission\Requests\UserRequest;
use App\Modules\RolePermission\Services\UserService;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Services\SmsService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserController extends Controller
{
    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;

        $this->middleware('permission:user-list')->only('index');
        $this->middleware('permission:user-create')->only(['create', 'store']);
        $this->middleware('permission:user-update')->only(['edit', 'update']);
        $this->middleware('permission:user-delete')->only('destroy');
    }

    public function index(Request $request)
    {
        $users = $this->userService->getAdmins($request);
        $roles = Role::where('guard_name', 'admin')->get();

        return view('RolePermission::users.index', compact('users', 'roles'));
    }

    public function changeRole(Request $request, $id)
    {
        $this->middleware('permission:user-update');

        $request->validate([
            'role_id' => 'nullable|exists:roles,id',
        ]);

        $user = User::findOrFail($id);

        if ($request->role_id) {
            $role = Role::findById((int) $request->role_id, 'admin');
            $user->syncRoles([$role->name]);
        } else {
            $user->syncRoles([]);
        }

        return response()->json(['success' => true, 'message' => 'Role updated successfully.']);
    }

    public function create()
    {
        $roles = Role::where('guard_name', 'admin')->get();
        $groupedPermissions = Permission::select('group_name', 'id', 'name')
            ->where('guard_name','admin')
            ->orderBy('group_name')->get()->groupBy('group_name');

        return view('RolePermission::users.create', compact('roles', 'groupedPermissions'));
    }

    public function store(UserRequest $request)
    {
        $this->userService->createAdmin($request->validated());
        return redirect()->route('users.index')->with('success', 'User created successfully');
    }

    public function edit($id)
    {
        $roles = Role::where('guard_name', 'admin')->get();
        $user  = $this->userService->getAdminById($id);

        $groupedPermissions = Permission::select('group_name', 'id', 'name')
            ->where('guard_name','admin')
            ->orderBy('group_name')->get()->groupBy('group_name');

        return view('RolePermission::users.edit', compact('user', 'roles', 'groupedPermissions'));
    }

    public function resetPassword($id)
    {
        $password = collect(range(0, 9))
            ->shuffle()
            ->take(8)
            ->implode('');

        $user = User::find($id);

        \Log::info('Resetting password for user ID: ' . $user);

        $user->update([
            'password'        => Hash::make($password),
            'password_expiry' => now()->addMinute(15),
        ]);

        try {
            $smsService  = new SmsService;
            $smsResponse = $smsService->sendMessage(
                $user->phone,
                "Your password has been reset. Your new temporary password is: $password Please change your password after logging in. This password will expire in 15 minutes."
            );
        } catch (\Throwable $th) {
            info($th->getMessage());
        }

        return redirect()->back()->with('message', 'A temporary password has been sent to the users phone number.');
    }

    public function update(UserRequest $request, $id)
    {
        $this->userService->updateAdmin($request->validated(), $id);

        return redirect()->route('users.index')->with('success', 'User updated successfully');
    }

    public function destroy($id)
    {
        $this->userService->deleteAdmin($id);

        return redirect()->route('users.index')->with('success', 'User deleted successfully');
    }
}
