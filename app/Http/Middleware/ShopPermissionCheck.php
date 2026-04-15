<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShopPermissionCheck
{
    public function handle(Request $request, Closure $next, $permissions = null)
    {

        $user = auth()->user();

        if ($user->role->value == UserRole::MERCHANT->value) {
            return $next($request);
        } else {
            if ($user->hasPermission($permissions, 'api')) {
                return $next($request);
            }
        }

        return response()->json(['message' => 'You do not have permission to access this resource.'], 403);
    }
}
