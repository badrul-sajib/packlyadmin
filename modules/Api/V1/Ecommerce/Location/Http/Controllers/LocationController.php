<?php

namespace Modules\Api\V1\Ecommerce\Location\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Order\Location;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function location(Request $request): JsonResponse
    {
        $parentId = $request->input('parent_id');

        $locations = Location::query()
            ->where('parent_id', $parentId)
            ->select('id', 'name', 'type', 'parent_id')
            ->get()->map(function ($data) {
                return [
                    'id'        => intval($data->id),
                    'parent_id' => (int) $data->parent_id,
                    'name'      => $data->name,
                    'type'      => $data->type,
                ];
            });

        return success('Locations showed successfully', $locations);
    }
}
