<?php

use App\Models\Shop\ShopSetting;
use Illuminate\Http\JsonResponse;

if (! function_exists('skipTypeConvert')) {
    function skipTypeConvert(&$data, $skipKeys = [])
    {
        if (! is_array($data)) {
            return;
        }
        array_walk_recursive($data, function (&$value, $key) use ($skipKeys) {
            if (is_numeric($value) && ! in_array($key, $skipKeys, true)) {
                $value = ! str_contains($value, '.') ? (int) $value : (float) $value;
            }
        });
    }
}

if (! function_exists('success')) {
    function success($message, $data = [], $statusCode = 200, $skipKeys = ['phone', 'password']): JsonResponse
    {
        skipTypeConvert($data, $skipKeys);

        return response()->json([
            'message' => $message,
            'data'    => $data,
        ], $statusCode, [], JSON_UNESCAPED_UNICODE);
    }
}

if (! function_exists('failure')) {
    function failure($message, $statusCode = 400): JsonResponse
    {
        return response()->json([
            'message' => $message,
            'data'    => null,
        ], $statusCode);
    }
}

if (! function_exists('validationError')) {
    function validationError($message, $errors = [], $statusCode = 422): JsonResponse
    {
        return response()->json([
            'message' => $message,
            'errors'  => $errors,
        ], $statusCode);
    }
}

if (! function_exists('formatPagination')) {
    function formatPagination($message, $entity, $statusCode = 200, $skipKeys = ['phone']): JsonResponse
    {
        $items = $entity->items();
        skipTypeConvert($items, $skipKeys);

        return response()->json([
            'message'       => $message,
            'data'          => $items,
            'total'         => $entity->total(),
            'last_page'     => $entity->lastPage(),
            'current_page'  => $entity->currentPage(),
            'next_page_url' => $entity->nextPageUrl(),
        ], $statusCode, [], JSON_UNESCAPED_UNICODE);
    }
}

if (! function_exists('resourceFormatPagination')) {
    function resourceFormatPagination($message, $data, $entity, $statusCode = 200, $skipKeys = ['phone']): JsonResponse
    {
        skipTypeConvert($data, $skipKeys);

        return response()->json([
            'message'       => $message,
            'data'          => $data,
            'total'         => $entity->total(),
            'last_page'     => $entity->lastPage(),
            'current_page'  => $entity->currentPage(),
            'next_page_url' => $entity->nextPageUrl(),
        ], $statusCode, [], JSON_UNESCAPED_UNICODE);
    }
}

if (! function_exists('getGatewayCharge')) {
    function getGatewayCharge(): float
    {
        return ShopSetting::where('key', 'gateway_charge')->first()->value ?? 0;
    }
}
