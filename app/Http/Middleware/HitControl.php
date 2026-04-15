<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class HitControl
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        if (in_array($request->method(), ['POST', 'PUT', 'PATCH']) && ! empty($request->all())) {

            $userPart   = $request->user()?->id         ?? 'guest';
            $routePart  = $request->route()?->getName() ?? $request->path();
            $methodPart = $request->method();
            // Per-user/IP + per-route + per-method bucket to avoid cross-endpoint contention
            $key          = implode('|', [$userPart, $request->ip(), $methodPart, $routePart]);
            $maxAttempts  = 10;
            $decaySeconds = 5;

            if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
                $retryAfter = RateLimiter::availableIn($key);

                return response()->json([
                    'status'      => Response::HTTP_TOO_MANY_REQUESTS,
                    'error'       => 'Too Many Requests',
                    'message'     => 'You have sent too many requests. Please try again later.',
                    'retry_after' => $retryAfter,
                    'timestamp'   => now(),
                ], Response::HTTP_TOO_MANY_REQUESTS)
                    ->header('X-RateLimit-Key', $key)
                    ->header('Retry-After', $retryAfter);
            }

            RateLimiter::hit($key, $decaySeconds);
        }

        return $next($request);
    }
}
