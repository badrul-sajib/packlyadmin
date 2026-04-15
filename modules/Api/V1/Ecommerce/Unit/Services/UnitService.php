<?php

namespace Modules\Api\V1\Ecommerce\Unit\Services;

use Modules\Api\V1\Ecommerce\Unit\Repositories\Contracts\UnitRepositoryInterface;
use App\Models\Unit\Unit;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class UnitService
{
    public function __construct(
        protected UnitRepositoryInterface $repository
    ) {}

    public static function getUnits(): JsonResponse
    {
        try {
            $units = [
                ['key' => 'piece', 'value' => 'Piece'],
                ['key' => 'pack',  'value' => 'Pack'],
                ['key' => 'box',   'value' => 'Box'],
                ['key' => 'kg',    'value' => 'KG'],
                ['key' => 'liter', 'value' => 'Liter'],
            ];

            return response()->json([
                'status' => true,
                'data' => $units,
            ]);
        } catch (Exception $e) {
            Log::error('getUnits error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return failure('Something went wrong', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    // Add your business logic here
}
