<?php

namespace App\Actions;

use App\Models\Voucher\Voucher;
use Illuminate\Http\JsonResponse;

class FetchVouchar
{
    public function execute($request): JsonResponse
    {

        $perPage = $request->input('perPage', 10);
        $page    = $request->input('page', 1);

        $vouchers = Voucher::where('status', 'active')->latest()
            ->paginate($perPage, ['*'], 'page', $page);

        $vouchers->getCollection()->transform(function ($vouchers) {
            return [
                'id'                   => $vouchers->id,
                'name'                 => $vouchers->name,
                'description'          => $vouchers->description,
                'code'                 => $vouchers->code,
                'start_date'           => $vouchers->start_date,
                'end_date'             => $vouchers->end_date,
                'discount_value'       => $vouchers->discount_value,
                'max_discount_value'   => $vouchers->max_discount_value,
                'max_purchase'         => $vouchers->max_purchase,
                'min_purchase'         => $vouchers->min_purchase,
                'usage_limit_per_user' => $vouchers->usage_limit_per_user,
                'usage_limit_total'    => $vouchers->usage_limit_total,
                'max_uses'             => $vouchers->max_uses,
                'used_count'           => $vouchers->used_count,
                'merchant_type'        => $vouchers->merchant_type,
                'status'               => $vouchers->status,
            ];
        });

        return formatPagination('Shops vouchers fetched successfully', $vouchers);
    }
}
