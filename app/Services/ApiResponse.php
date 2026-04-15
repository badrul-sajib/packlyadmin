<?php

namespace App\Services;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\MessageBag;
use Symfony\Component\HttpFoundation\Response;

class ApiResponse
{
    public static function success(string $message, $data = [], int $statusCode = Response::HTTP_OK, ...$metadata): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $data,
            'metadata' => $metadata,
        ], $statusCode);
    }

    public static function successMessageForCreate(string $message, $data = [], int $statusCode = Response::HTTP_CREATED, ...$metadata): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $data,
            'metadata' => $metadata,
        ], $statusCode);
    }

    public static function failure(string $message, int $statusCode = Response::HTTP_BAD_REQUEST, ...$metadata): JsonResponse
    {
        return response()->json([
            'status' => 'failed',
            'message' => $message,
            'metadata' => $metadata,
        ], $statusCode);
    }

    public static function error(string $message, int $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR, array|MessageBag $details = [], ...$metadata): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
            'errorDetails' => $details,
            'metadata' => $metadata,
        ], $statusCode);
    }

    public static function validationError(string $message, array|MessageBag $errors = [], int $statusCode = Response::HTTP_UNPROCESSABLE_ENTITY, ...$metadata): JsonResponse
    {
        return response()->json([
            'status' => 'failed',
            'message' => $message,
            'errors' => $errors,
            'metadata' => $metadata,
        ], $statusCode);
    }

    public static function formatPagination(string $message, $entity, int $statusCode = Response::HTTP_OK, ...$metadata): JsonResponse
    {
        return response()->json([
            'message' => $message,
            'items' => $entity->items(),
            'total' => $entity->total(),
            'last_page' => $entity->lastPage(),
            'current_page' => $entity->currentPage(),
            'next_page_url' => $entity->nextPageUrl(),
            'metadata' => $metadata,
        ], $statusCode);
    }

    public static function authenticationError(string $message, array $errors, int $statusCode = Response::HTTP_UNAUTHORIZED, ...$metadata): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
            'errors' => $errors,
            'code' => $statusCode,
            'metadata' => $metadata,
        ], $statusCode);
    }

    public static function customFailure(string $message, $data = [], int $statusCode = Response::HTTP_OK, ...$metadata): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $data,
            'metadata' => $metadata,
        ], $statusCode);
    }
    // In ApiResponse.php — add this NEW method, don't touch formatPagination
    public static function formatPaginationWithCounts(string $message, $entity, array $counts = [], int $statusCode = Response::HTTP_OK): JsonResponse
    {
        return response()->json([
            'message' => $message,
            'items' => $entity->items(),
            'total' => $entity->total(),
            'last_page' => $entity->lastPage(),
            'current_page' => $entity->currentPage(),
            'next_page_url' => $entity->nextPageUrl(),
            'counts' => $counts,
        ], $statusCode);
    }

}
