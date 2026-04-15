<?php

namespace App\Exceptions;

use App\Services\ErrorLogService;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\Log;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            // if (config('app.env') == 'local') {
            //     return;
            // }

            try {
                ErrorLogService::log([
                    'source'        => 'backend',
                    'client_type'   => 'server',
                    'user_id'       => $this->getUserId(),
                    'status_code'   => $this->getStatusCode($e),
                    'endpoint'      => request()->fullUrl(),
                    'current_route' => request()->fullUrl(),
                    'user_agent'    => request()->userAgent(),
                    'environment'   => config('app.env'),
                    'message'       => $e->getMessage(),
                    'stack'         => $e->getTraceAsString(),
                    'ip_address'    => request()->ip(),
                    'file'          => $e->getFile(),
                    'line'          => $e->getLine(),
                ]);
            } catch (\Exception $logError) {
                Log::error('Error sending to /log-error', [
                    'message'  => $logError->getMessage(),
                    'original' => $e->getMessage(),
                ]);
            }
        });
    }

    private function getStatusCode(Throwable $e): int
    {
        if (method_exists($e, 'getStatusCode')) {
            return $e->getStatusCode();
        }

        if ($e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) {
            return 404;
        }

        if ($e instanceof \Illuminate\Validation\ValidationException) {
            return 422;
        }

        if ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpException) {
            return $e->getStatusCode();
        }

        if ($e instanceof \Illuminate\Auth\AuthenticationException) {
            return 401;
        }

        if ($e instanceof \Illuminate\Auth\Access\AuthorizationException) {
            return 403;
        }

        return 500;
    }

    private function getUserId(): ?int
    {
        $guards = ['sanctum'];

        foreach ($guards as $guard) {
            try {
                if (auth($guard)->check()) {
                    return auth($guard)->id();
                }
            } catch (\Exception $e) {
                continue;
            }
        }

        return null;
    }
}
