<?php

namespace App\Services;

use App\Exceptions\CommissionValidateException;
use App\Models\Merchant\Commission;
use Illuminate\Support\Facades\DB;

class CommissionService
{
    /**
     * @throws CommissionValidateException
     */
    public function store(array $data)
    {
        $this->validateCommissionUniqueness($data);

        // Uncomment to actually create the commission after validation
        return Commission::create([
            'commission_rate' => $data['commission_rate'],
            'category_id'     => $data['category_ids']    ?? null,
            'merchant_id'     => $data['merchant_ids']    ?? null,
            'product_id'      => $data['product_ids']     ?? null,
            'start_date'      => $data['start_date']      ?? null,
            'end_date'        => $data['end_date']        ?? null,
        ]);
    }

    /**
     * @throws CommissionValidateException
     */
    protected function validateCommissionUniqueness(array $data): void
    {
        $query = DB::table('commissions');

        // Apply date range conditions if provided
        if ($this->hasDateRange($data)) {
            $query->where('start_date', '>=', $data['start_date'])
                ->where('end_date', '<=', $data['end_date']);
        } else {
            $query->whereNull('start_date')->whereNull('end_date');
        }

        // Apply filter conditions
        $this->applyFilterConditions($query, $data);

        if ($query->exists()) {
            throw new CommissionValidateException($this->getErrorMessage($data));
        }
    }

    protected function hasDateRange(array $data): bool
    {
        return ! empty($data['start_date']) && ! empty($data['end_date']);
    }

    protected function applyFilterConditions($query, array $data): void
    {
        $filters = [
            'category_id' => $data['category_ids']  ?? null,
            'product_id'  => $data['product_ids']   ?? null,
            'merchant_id' => $data['merchant_ids']  ?? null,
        ];

        foreach ($filters as $field => $value) {
            if ($value !== null) {
                $query->where($field, $value);
            } else {
                $query->whereNull($field);
            }
        }
    }

    protected function getErrorMessage(array $data): string
    {
        if ($this->hasDateRange($data)) {
            return 'Commission already exists for the selected date range and filters.';
        }

        return 'Commission already exists for the selected filters.';
    }

    /**
     * Update an existing slider.
     */
    public function update(Commission $commission, array $data): Commission
    {
        $commission->update($data);

        return $commission;
    }

    /**
     * Delete a slider.
     */
    public function delete(Commission $commission): bool
    {
        return $commission->delete();
    }
}
