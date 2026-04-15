<?php

namespace App\Services\Merchant;

use App\Enums\MerchantStatus;
use App\Enums\ShopProductStatus;
use App\Models\Merchant\Merchant;
use App\Models\Merchant\MerchantReport;
use App\Models\Product\ProductHoldStatus;
use App\Models\Shop\ShopProduct;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Throwable;

class MerchantReportService
{
    protected MerchantReport $merchantReport;

    public function __construct(MerchantReport $merchantReport)
    {
        $this->merchantReport = $merchantReport;
    }

    /**
     * @throws Throwable
     */
    public function store(array $data): MerchantReport
    {
        return DB::transaction(function () use ($data) {
            $data['added_by'] = Auth::id();

            $report = $this->merchantReport->create($data);

            $this->updateMerchantStatus($data['merchant_id']);
            $this->updateProductHoldStatuses($data['merchant_id']);
            $this->disableShopProducts($data['merchant_id']);

            return $report;
        });
    }

    /**
     * Find merchant report by ID
     */
    public function show(int $id): ?MerchantReport
    {
        return $this->merchantReport->findOrFail($id);
    }

    /**
     * Update merchant shop status
     */
    private function updateMerchantStatus(int $merchantId): void
    {
        $merchant              = Merchant::find($merchantId);
        $oldStatus             = $merchant->shop_status;
        $merchant->shop_status = MerchantStatus::Suspended->value;

        $merchant->save();

        $causerName = auth()->user()->name;
        $date       = now()->format('d M Y h:i A');

        $message = "Status changed by {$causerName} {$date}";
        $logName = 'merchant-status-update';

        $properties = [
            'new' => MerchantStatus::Suspended->name,
            'old' => $oldStatus->name,
        ];

        activity()
            ->useLog($logName)
            ->event('suspended')
            ->performedOn($merchant)
            ->causedBy(auth()->user())
            ->withProperties($properties)
            ->log($message);

        try {
            $merchant->sendNotification(
                'Shop Suspended',
                'Your Merchant account has been suspended! Please check notice for more details.',
                '/notice'
            );
        } catch (\Throwable $th) {
            info($th->getMessage());
        }
    }

    /**
     * Update or create product hold statuses
     */
    private function updateProductHoldStatuses(int $merchantId): void
    {
        ShopProduct::where('merchant_id', $merchantId)
            ->chunk(100, function ($products) use ($merchantId) {
                foreach ($products as $product) {
                    ProductHoldStatus::updateOrCreate(
                        [
                            'shop_product_id' => $product->id,
                            'merchant_id'     => $merchantId,
                        ],
                        ['status_id' => $product->status]
                    );
                }
            });
    }

    /**
     * Disable all shop products for merchant
     */
    private function disableShopProducts(int $merchantId): void
    {
        ShopProduct::where('merchant_id', $merchantId)
            ->update(['status' => ShopProductStatus::DISSABLED->value]);
    }
}
