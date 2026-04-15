<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Console\Scheduling\Schedule;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->validateCsrfTokens(except: [
            '/success',
            '/cancel',
            '/fail',
            '/ipn',
        ]);

        $middleware->api([
            \App\Http\Middleware\IsActiveCustomer::class,
            \App\Http\Middleware\ApiDebugbar::class,
        ]);

        $middleware->alias([
            'auth'             => \App\Http\Middleware\Authenticate::class,
            'auth.basic'       => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
            'auth.session'     => \Illuminate\Session\Middleware\AuthenticateSession::class,
            'cache.headers'    => \Illuminate\Http\Middleware\SetCacheHeaders::class,
            'can'              => \Illuminate\Auth\Middleware\Authorize::class,
            'guest'            => \App\Http\Middleware\RedirectIfAuthenticated::class,
            'signed'           => \App\Http\Middleware\ValidateSignature::class,
            'throttle'         => \Illuminate\Routing\Middleware\ThrottleRequests::class,
            'token.expiration' => \App\Http\Middleware\TokenExpiration::class,
            'verified'         => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
            'api.debugbar'     => \App\Http\Middleware\ApiDebugbar::class,
            'permission'       => \App\Http\Middleware\PermissionCheck::class,
            'api.check'        => \App\Http\Middleware\ApiCheck::class,
            'business.manager.api.check' => \App\Http\Middleware\BusinessManagerApiCheck::class,
            'role.check'       => \App\Http\Middleware\CheckUserRole::class,
            'hit.control'      => \App\Http\Middleware\HitControl::class,
            'json.throttle'    => \App\Http\Middleware\JsonThrottleRequests::class,
            'shop.permission'  => \App\Http\Middleware\ShopPermissionCheck::class,
            'track.visitor'    => \App\Http\Middleware\TrackVisitor::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->reportable(function (Throwable $e) {
            try {
                \App\Services\ErrorLogService::log([
                    'source'        => 'backend',
                    'client_type'   => 'server',
                    'user_id'       => auth('sanctum')->id(),
                    'status_code'   => method_exists($e, 'getStatusCode') ? $e->getStatusCode() : 500,
                    'endpoint'      => request()->fullUrl(),
                    'current_route' => optional(request()->route())->getName(),
                    'user_agent'    => request()->userAgent(),
                    'environment'   => app()->environment(),
                    'message'       => $e->getMessage(),
                    'stack'         => collect($e->getTrace())->take(10),
                    'ip_address'    => request()->ip(),
                    'file'          => $e->getFile(),
                    'line'          => $e->getLine(),
                ]);
            } catch (Throwable $logError) {
                logger()->error('ErrorLogService failed', [
                    'error' => $logError->getMessage(),
                    'original' => $e->getMessage(),
                ]);
            }
        });
    })
    ->withSchedule(function (Schedule $schedule) {
        $schedule->command('feeds:generate')
            ->dailyAt('04:00')
            ->withoutOverlapping();
    })
    ->create();
