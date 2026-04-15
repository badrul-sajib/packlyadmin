<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TokenExpiration
{
    public function handle(Request $request, Closure $next)
    {
        $user  = Auth::user();
        $token = $user->currentAccessToken();

        // Check if token has expired
        if ($token && $token->expires_at && $token->expires_at->isPast()) {
            $token->delete();

            return response()->json(['message' => 'Token has expired.'], 401);
        }

        return $next($request);
    }
}
