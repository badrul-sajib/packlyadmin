<?php

namespace App\Services\Voucher;

use App\Models\Voucher\Voucher;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Throwable;

class VoucherServices
{
    public function getAllVouchers($request)
    {
        $perPage       = $request->perPage       ?? 10;
        $search        = $request->search        ?? null;
        $page          = $request->page          ?? 1;
        $startDate     = $request->start_date ? date('Y-m-d 00:00:00', strtotime($request->start_date)) : now()->subDays(6)->startOfDay();
        $endDate       = $request->end_date ? date('Y-m-d 23:59:59', strtotime($request->end_date)) : now()->endOfDay();

        return Voucher::query()
            ->with('merchants', 'user')
            ->when($search, function ($query) use ($search) {
                $query->whereAny(['name', 'code'], 'like', '%'.$search.'%');
            })

            ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            })
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
                'status'               => $data->status,
                'start_date'           => $data->start_date,
                'end_date'             => $data->end_date,
                'merchant_type'        => $data->merchant_type,
                'added_by'             => auth()->id(),
            ]);

            // Attach related entities if they exist
            if (! empty($data->merchant_ids)) {
                $voucher->merchants()->attach($data->merchant_ids);
            }

            DB::commit();

            return response()->json(['message' => 'Voucher created successfully']);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(['message' => 'Failed to create voucher'], 500);
        }
    }

    public function getVoucherById($id): Voucher
    {
        return Voucher::findOrFail($id);
    }

    /**
     * @throws Throwable
     */
    public function updateVoucher($data, $id): JsonResponse
    {
        DB::beginTransaction();

        try {
            // Find the voucher by ID
            $voucher = Voucher::findOrFail($id);

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
                'status'               => $data['status'],
                'start_date'           => $data['start_date'],
                'end_date'             => $data['end_date'],
                'merchant_type'        => $data['merchant_type'],
            ]);

            // Update related entities (merchants, categories, brands, products)
            if (! empty($data['merchant_ids'])) {
                $voucher->merchants()->sync($data['merchant_ids']);
            }

            if (empty($data['merchant_type'])) {
                $voucher->merchants()->detach();  // Detach merchants
            }

            DB::commit();

            return response()->json(['message' => 'Voucher updated successfully']);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(['message' => 'Failed to update voucher'], 500);
        }
    }

    public function deleteVoucher($id): void
    {
        $voucher = $this->getVoucherById($id);
        $voucher->delete();
    }
}
