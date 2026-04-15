<?php

namespace App\Http\Middleware;

use App\Models\Visitor;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackVisitor
{
    protected array $skipExtensions = [
        'js', 'css', 'png', 'jpg', 'jpeg', 'gif', 'svg', 'webp', 'ico', 'map',
        'woff', 'woff2', 'ttf', 'otf', 'eot',
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($this->shouldSkip($request)) {
            return $next($request);
        }

        $ipAddress = $request->ip();
        if (! $ipAddress) {
            return $next($request);
        }

        $visitor = Visitor::firstOrNew(['ip_address' => $ipAddress]);
        $visitor->visit_count = $visitor->exists ? $visitor->visit_count + 1 : 1;
        $visitor->url = $request->fullUrl();
        $visitor->last_visit_at = now();
        $visitor->save();

        if ($visitor->is_blocked) {
            return $this->blockedResponse($request);
        }

        return $next($request);
    }

    protected function shouldSkip(Request $request): bool
    {
        if ($request->isMethod('OPTIONS')) {
            return true;
        }

        $extension = pathinfo($request->path(), PATHINFO_EXTENSION);
        if ($extension && in_array(strtolower($extension), $this->skipExtensions, true)) {
            return true;
        }

        return false;
    }

    protected function blockedResponse(Request $request): Response
    {
        if ($request->expectsJson()) {
            return response()->json(['message' => 'Your IP has been blocked.'], Response::HTTP_FORBIDDEN);
        }

        abort(Response::HTTP_FORBIDDEN, 'Your IP has been blocked.');
    }
}
