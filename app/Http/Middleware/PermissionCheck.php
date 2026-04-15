<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class PermissionCheck
{
    public function handle(Request $request, Closure $next, $permissions)
    {
        // Static mode: all authenticated users have full access
        return $next($request);
    }
}
