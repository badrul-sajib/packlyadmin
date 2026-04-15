<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use App\Services\ApiResponse;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckUserRole
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        if (Auth::check() && in_array(Auth::user()->role->value, [UserRole::MERCHANT->value, UserRole::SHOP_ADMIN->value])) {
            return $next($request);
        }

        return ApiResponse::failure('Unauthorized', Response::HTTP_UNAUTHORIZED);
    }
}
