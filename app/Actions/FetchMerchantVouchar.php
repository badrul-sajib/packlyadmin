<?php

namespace App\Actions;

use App\Models\Voucher\Voucher;
use Illuminate\Http\JsonResponse;

class FetchMerchantVouchar
{
    public function execute($request, $id): JsonResponse
    {
        $perPage = $request->input('perPage', 10);

        $page = $request->input('page', 1);

        $vouchars = Voucher::whereHas('merchants', function ($query) use ($id) {

            $query->where('merchant_id', $id);

        })->where('status', 'active')->latest()

            ->paginate($perPage, ['*'], 'page', $page);

        $vouchars->getCollection()->transform(function ($vouchars) {
            return [
                'id'                   => $vouchars->id,
                'name'                 => $vouchars->name,
                'description'          => $vouchars->description,
                'code'                 => $vouchars->code,
                'start_date'           => $vouchars->start_date,
                'end_date'             => $vouchars->end_date,
                'discount_value'       => $vouchars->discount_value,
                'max_discount_value'   => $vouchars->max_discount_value,
                'max_purchase'         => $vouchars->max_purchase,
                'min_purchase'         => $vouchars->min_purchase,
                'usage_limit_per_user' => $vouchars->usage_limit_per_user,
                'usage_limit_total'    => $vouchars->usage_limit_total,
                'max_uses'             => $vouchars->max_uses,
                'used_count'           => $vouchars->used_count,
                'merchant_type'        => $vouchars->merchant_type,
                'status'               => $vouchars->status,
            ];
        });

        return formatPagination('Vouchar fetched successfully', $vouchars);
    }
}
