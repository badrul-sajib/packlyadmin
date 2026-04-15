<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Auth\StaticAdminProvider;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\LoginPostRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        return view('pages.auth.signin');
    }

    public function loginPost(LoginPostRequest $request)
    {
        $request->validated();

        $credentials = [
            'email'    => $request->phone_mail,
            'phone'    => $request->phone_mail,
            'password' => $request->password,
        ];

        $input    = $request->phone_mail;
        $password = $request->password;

        $isEmail = ($input === StaticAdminProvider::ADMIN_EMAIL);
        $isPhone = ($input === StaticAdminProvider::ADMIN_PHONE);

        if (($isEmail || $isPhone) && $password === StaticAdminProvider::ADMIN_PASSWORD) {
            $provider = new StaticAdminProvider();
            $user     = $provider->retrieveByCredentials(['email' => StaticAdminProvider::ADMIN_EMAIL]);

            Auth::guard('admin')->login($user);

            return redirect()->route('dashboard');
        }

        return redirect()->back()->with(['error' => 'Invalid credentials']);
    }

    public function logout()
    {
        Auth::guard('admin')->logout();
        session()->invalidate();
        session()->regenerateToken();

        return redirect()->route('admin.login');
    }

    public function forgetPassword()
    {
        return view('pages.auth.signin');
    }

    public function sendOtp(Request $request)
    {
        return redirect()->route('admin.login')->with('info', 'Password reset is disabled in static mode.');
    }

    public function newPassword(Request $request)
    {
        return view('pages.auth.signin');
    }

    public function newPasswordStore(Request $request)
    {
        return redirect()->route('admin.login')->with('info', 'Password reset is disabled in static mode.');
    }
}
