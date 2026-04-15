<?php

namespace App\Services;

use App\Models\Voucher\Voucher;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class VoucherService
{
    public function getAllVouchers($request): LengthAwarePaginator|array|\Illuminate\Pagination\LengthAwarePaginator
    {
        $perPage   = $request->perPage ?? 10;
        $search    = $request->search  ?? null;
        $page      = $request->page    ?? 1;
        $startDate = $request->start_date ? date('Y-m-d 00:00:00', strtotime($request->start_date)) : now()->subDays(6)->startOfDay();
        $endDate   = $request->end_date ? date('Y-m-d 23:59:59', strtotime($request->end_date)) : now()->endOfDay();

        $merchant_id = auth()->user()->merchant->id;

        return Voucher::query()
            ->with('merchants', 'user')
            ->whereHas('merchants', function ($query) use ($merchant_id) {
                $query->where('merchants.id', $merchant_id);
            })
            ->when($search, function ($query) use ($search) {
                $query->whereAny(['name', 'code'], 'like', '%'.$search.'%');
            })
            ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            })
            ->where('is_admin', false)
            ->latest()
            ->paginate($perPage, ['*'], 'page', $page)
            ->withQueryString();
    }

    /**
     * @throws Throwable
     */
    public function createVoucher($data): JsonResponse
    {
        DB::beginTransaction();

        try {
            $voucher = Voucher::create([
                'name'                 => $data->name,
                'code'                 => $data->code,
                'discount_value'       => $data->discount_value,
                'max_discount_value'   => $data->max_discount_value,
                'description'          => $data->description,
                'min_purchase'         => $data->min_purchase,
                'max_purchase'         => $data->max_purchase,
                'usage_limit_per_user' => $data->usage_limit_per_user,
                'usage_limit_total'    => $data->usage_limit_total,
                'status'               => 'pending',
                'start_date'           => $data->start_date,
                'end_date'             => $data->end_date,
                'merchant_type'        => 2,
                'added_by'             => auth()->id(),
                'is_admin'             => false,
            ]);

            $voucher->merchants()->attach([$data->merchant_id]);

            DB::commit();

            return ApiResponse::success('Voucher created successfully', $voucher, Response::HTTP_OK);

        } catch (\Exception $e) {
            DB::rollBack();

            return ApiResponse::error('Failed to create voucher', Response::HTTP_INTERNAL_SERVER_ERROR );
        }
    }

    public function getVoucherById(int $id): Voucher
    {
        return Voucher::findOrFail($id);
    }

    /**
     * @throws Throwable
     */
    public function updateVoucher($data, int $id): JsonResponse
    {
        DB::beginTransaction();

        try {
            // Find the voucher by ID
            $voucher = Voucher::findOrFail($id);

            // if is_admin is true return
            if ($voucher->is_admin) {
                return response()->json(['message' => 'Cannot update admin voucher'], Response::HTTP_FORBIDDEN);
            }

            // added_by is not allowed to update
            if ($voucher->added_by != auth()->user()->merchant->id) {
                return response()->json(['message' => 'Cannot update this voucher'], Response::HTTP_FORBIDDEN);
            }

            // Update voucher with validated data
            $voucher->update([
                'name'                 => $data['name'],
                'code'                 => $data['code'],
                'discount_value'       => $data['discount_value'],
                'max_discount_value'   => $data['max_discount_value'],
                'description'          => $data['description'],
                'min_purchase'         => $data['min_purchase'],
                'max_purchase'         => $data['max_purchase'],
                'usage_limit_per_user' => $data['usage_limit_per_user'],
                'usage_limit_total'    => $data['usage_limit_total'],
                'start_date'           => $data['start_date'],
                'end_date'             => $data['end_date'],
            ]);

            DB::commit();

            return ApiResponse::success('Voucher updated successfully', [], Response::HTTP_OK);
        } catch (\Exception $e) {
            DB::rollBack();

            return ApiResponse::failure('Failed to update voucher', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function deleteVoucher(int $id): bool
    {
        $voucher = $this->getVoucherById($id);
        $voucher->delete();

        return true;
    }
}
