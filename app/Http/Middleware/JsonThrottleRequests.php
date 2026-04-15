<?php

namespace App\Http\Middleware;

use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpFoundation\Response;

class JsonThrottleRequests extends \Illuminate\Routing\Middleware\ThrottleRequests
{
    protected function buildException($request, $key, $maxAttempts, $responseCallback = null)
    {
        $retryAfter = $this->getTimeUntilNextRetry($key);

        $headers = $this->getHeaders(
            $maxAttempts,
            $this->calculateRemainingAttempts($key, $maxAttempts, $retryAfter),
            $retryAfter
        );

        if (is_callable($responseCallback)) {
            return new HttpResponseException($responseCallback($request, $headers));
        }

        $json = response()->json([
            'status'      => Response::HTTP_TOO_MANY_REQUESTS,
            'error'       => 'Too Many Requests',
            'message'     => 'You have sent too many requests. Please try again later.',
            'retry_after' => $retryAfter,
        ], Response::HTTP_TOO_MANY_REQUESTS, $headers);

        return new HttpResponseException($json);
    }
}
