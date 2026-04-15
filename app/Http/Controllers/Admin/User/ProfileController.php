<?php

namespace App\Http\Controllers\Admin\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PasswordUpdateRequest;
use App\Http\Requests\Admin\ProfileUpdateRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Throwable;

class ProfileController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        return view('backend.pages.profile.index', compact('user'));
    }

    public function update(ProfileUpdateRequest $request)
    {
        $user = auth()->user();
        $request->validated();

        try {
            $user->update([
                'name'   => $request->name,
                'phone'  => $request->phone,
                'avatar' => $request->avatar,
            ]);

            return response()->json(['success' => 'Profile updated successfully !']);
        } catch (Throwable $th) {
            return response()->json(['message' => 'Something went wrong', 'type' => 'error'], 200);
        }
    }

    public function changePassword()
    {
        return view('backend.pages.profile.change_password');
    }

    public function passwordUpdate(PasswordUpdateRequest $request)
    {
        $request->validated();

        $user = auth()->user();

        $user->password = Hash::make($request->password);
        $user->save();

        // Revoke all existing tokens after admin password update
        $user->tokens()->delete();

        // Logout other active admin sessions except the current
        Auth::guard('admin')->logoutOtherDevices($request->password);

        return \redirect()->route('admin.dashboard')->with('success', 'Password updated successfully');
    }
}
