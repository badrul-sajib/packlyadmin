<?php

namespace App\Services;

use App\Enums\CommissionCase;
use Illuminate\Support\Carbon;
use App\Models\Product\Product;
use App\Models\Shop\ShopSetting;

class ProductCommissionService
{
    protected float $defaultCommissionRate = 5.0;

    public function calculateCommissionRate(int $productId): array
    {
        $product     = Product::with(['category', 'merchant'])->findOrFail($productId);
        $currentDate = Carbon::now();

        // First check all date-bound rules (Cases 1-5)
        foreach ($this->getDateRangeCases() as $case) {
            if ($rule = $this->findMatchingRule($case, $product, $currentDate)) {
                return $this->formatResponse($rule, $case);
            }
        }

        // Only check non-date rules if no date-bound rules matched (Cases 6-12)
        foreach ($this->getNonDateCases() as $case) {
            if ($rule = $this->findMatchingRule($case, $product, $currentDate)) {
                return $this->formatResponse($rule, $case);
            }
        }

        $commission = auth()->user()->merchant?->configuration?->commission_rate ?? null;

        if ($commission == null) {
            $commission = ShopSetting::where('key', 'commission_rate')->value('value');
        }

        $this->defaultCommissionRate = $commission ?? $this->defaultCommissionRate;

        // Fallback to default if no rules match
        return [
            'rate'            => $this->defaultCommissionRate,
            'case'            => null,
            'rule_id'         => null,
            'matched_filters' => [],
        ];
    }

    protected function getCommissionCases(): array
    {
        return [
            // Date-range cases (checked first)
            [
                'enum_case' => CommissionCase::DATE_RANGE_WITH_ALL_FILTERS,
                'filters'   => ['product' => true, 'category' => true, 'merchant' => true, 'date_range' => true],
                'message'   => 'Date range with product, category and merchant',
            ],
            [
                'enum_case' => CommissionCase::DATE_RANGE_WITH_CATEGORY_PRODUCT,
                'filters'   => ['product' => true, 'category' => true, 'merchant' => false, 'date_range' => true],
                'message'   => 'Date range with category and product',
            ],
            [
                'enum_case' => CommissionCase::DATE_RANGE_WITH_CATEGORY_MERCHANT,
                'filters'   => ['product' => false, 'category' => true, 'merchant' => true, 'date_range' => true],
                'message'   => 'Date range with category and merchant',
            ],
            [
                'enum_case' => CommissionCase::DATE_RANGE_WITH_PRODUCT_MERCHANT,
                'filters'   => ['product' => true, 'category' => false, 'merchant' => true, 'date_range' => true],
                'message'   => 'Date range with product and merchant',
            ],
            [
                'enum_case' => CommissionCase::DATE_RANGE_ONLY,
                'filters'   => ['product' => false, 'category' => false, 'merchant' => false, 'date_range' => true],
                'message'   => 'Date range only',
            ],

            // Non-date cases (checked only if no date-range rules match)
            [
                'enum_case' => CommissionCase::ALL_FILTERS,
                'filters'   => ['product' => true, 'category' => true, 'merchant' => true, 'date_range' => false],
                'message'   => 'Product, category and merchant',
            ],
            [
                'enum_case' => CommissionCase::CATEGORY_PRODUCT,
                'filters'   => ['product' => true, 'category' => true, 'merchant' => false, 'date_range' => false],
                'message'   => 'Category and product',
            ],
            [
                'enum_case' => CommissionCase::CATEGORY_MERCHANT,
                'filters'   => ['product' => false, 'category' => true, 'merchant' => true, 'date_range' => false],
                'message'   => 'Category and merchant',
            ],
            [
                'enum_case' => CommissionCase::PRODUCT_MERCHANT,
                'filters'   => ['product' => true, 'category' => false, 'merchant' => true, 'date_range' => false],
                'message'   => 'Product and merchant',
            ],
            [
                'enum_case' => CommissionCase::PRODUCT_ONLY,
                'filters'   => ['product' => true, 'category' => false, 'merchant' => false, 'date_range' => false],
                'message'   => 'Product only',
            ],
            [
                'enum_case' => CommissionCase::CATEGORY_ONLY,
                'filters'   => ['product' => false, 'category' => true, 'merchant' => false, 'date_range' => false],
                'message'   => 'Category only',
            ],
            [
                'enum_case' => CommissionCase::MERCHANT_ONLY,
                'filters'   => ['product' => false, 'category' => false, 'merchant' => true, 'date_range' => false],
                'message'   => 'Merchant only',
            ],
        ];
    }

    protected function getDateRangeCases(): array
    {
        return array_slice($this->getCommissionCases(), 0, 5);
    }

    protected function getNonDateCases(): array
    {
        return array_slice($this->getCommissionCases(), 5);
    }

    protected function findMatchingRule(array $case, Product $product, Carbon $currentDate)
    {
        $query = \App\Models\Merchant\Commission::query();

        // Product filter
        if ($case['filters']['product']) {
            $query->where('product_id', $product->id);
        } else {
            $query->whereNull('product_id');
        }

        // Category filter
        if ($case['filters']['category']) {
            $query->where('category_id', $product->category_id);
        } else {
            $query->whereNull('category_id');
        }

        // Merchant filter
        if ($case['filters']['merchant']) {
            $query->where('merchant_id', $product->merchant_id);
        } else {
            $query->whereNull('merchant_id');
        }

        // Date range handling
        if ($case['filters']['date_range']) {
            $query->where(function ($q) use ($currentDate) {
                $q->whereDate('start_date', '<=', $currentDate->toDateString())
                    ->whereDate('end_date', '>=', $currentDate->toDateString());
            });
        } else {
            $query->whereNull('start_date')
                ->whereNull('end_date');
        }

        $query->orderBy('commission_rate', 'desc');

        return $query->first();
    }

    protected function formatResponse($rule, array $case): array
    {
        return [
            'rate'            => $rule->commission_rate,
            'case'            => $case['enum_case'],
            'rule_id'         => $rule->id,
            'matched_filters' => $case['filters'],
            'message'         => $case['message'],
        ];
    }
}
