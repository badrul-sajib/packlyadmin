<?php

namespace Modules\Api\V1\Ecommerce\Campaign\Http\Controllers;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Giveaway\GiveawayTicket;
use App\Http\Resources\Ecommerce\GiveawayTicketResource;

class GiveawayController extends Controller
{
    public function tickets()
    {
        try {
            $per_page = request()->query('per_page', 25);
            $page = request()->query('page', 1);

            $tickets = GiveawayTicket::where('user_id', auth()->user()->id)
                ->with([
                    'giveaway',
                    'order' => function ($query) {
                        $query->with([
                            'merchantOrders' => function ($query) {
                                $query->whereIn('status_id', [OrderStatus::DELIVERED->value, OrderStatus::PARTIAL_DELIVERED->value]);
                            },
                        ]);
                    },
                ])
                ->paginate($per_page, ['*'], 'page', $page);

            return resourceFormatPagination('Giveaway tickets retrieved',GiveawayTicketResource::collection($tickets),$tickets);
          
        } catch (\Throwable $th) {
            return failure(
                'Giveaway tickets retrieval failed'.
                $th->getMessage()
            );
        }
    }
}
