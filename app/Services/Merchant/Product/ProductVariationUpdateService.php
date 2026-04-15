<?php

namespace App\Services\Merchant\Product;

use App\Enums\AccountTypes;
use App\Models\Account\Account;
use App\Models\Attribute\Attribute;
use App\Models\Attribute\AttributeOption;
use App\Models\Attribute\VariationAttribute;
use App\Models\Merchant\MerchantTransaction;
use App\Models\Product\Product;
use App\Models\Product\ProductVariation;
use App\Models\Product\ShopProduct;
use App\Models\Product\ShopProductVariation;
use App\Models\Purchase\PurchaseDetail;
use App\Models\Setting\ShopSetting;
use App\Models\Stock\StockInventory;
use App\Models\Stock\StockOrder;
use App\Services\ApiResponse;
use App\Services\ProductCommissionService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class ProductVariationUpdateService
{
    protected $productCommissionService;

    public function __construct(ProductCommissionService $productCommissionService)
    {
        $this->productCommissionService = $productCommissionService;
    }

    public function updateVariations(Request $request, string $slug, $merchantId): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'variations' => 'required|json',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            DB::beginTransaction();

            $product = $this->fetchProduct($slug, $merchantId);
            $shouldCreateDraft = $this->shouldCreateDraft($product);
            $draftSnapshots = $shouldCreateDraft ? $this->captureDraftSnapshots($product) : [];
            $data    = $this->prepareData($request);

            $variationData      = $this->processVariations($product, $data, $merchantId, $request);
            $totalStockQty      = $variationData['totalStockQty'];
            $totalPurchasePrice = $variationData['totalPurchasePrice'];

            $this->handleAccounting($product, $variationData['variationSkus'], $totalPurchasePrice, $merchantId, $request->payment_date);

            $product->total_stock_qty = $totalStockQty;
            $product->save();

            if ($shouldCreateDraft) {
                $this->syncDraftChanges($product->fresh([
                    'productDetail',
                    'variations.variationAttributes.attribute',
                    'variations.variationAttributes.attributeOption',
                    'variations.media',
                ]), $draftSnapshots);
            }

            DB::commit();

            return ApiResponse::successMessageForCreate('Product variations updated successfully.', Response::HTTP_OK);
        } catch (Exception $e) {
            DB::rollback();
            Log::error('Error updating product variations: '.$e->getMessage());

            return ApiResponse::failure('Failed to update product variations.', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    protected function fetchProduct(string $slug, $merchantId): Product
    {
        $product = Product::where(['slug' => $slug, 'merchant_id' => $merchantId])->first();
        if (! $product) {
            throw new Exception('Product not found.');
        }

        return $product;
    }

    protected function prepareData(Request $request): array
    {
        $data               = $request->all();
        $data['variations'] = json_decode($data['variations'], true);

        return $data;
    }

    protected function processVariations(Product $product, array $data, $merchantId, Request $request): array
    {
        $variationSkus      = [];
        $totalStockQty      = 0;
        $totalPurchasePrice = 0;
        $index              = 0;

        foreach ($data['variations'] as $combination) {

            \Log::info('Processing combination: ', $combination);

            $this->validateVariation($combination, $product);

            $variation = ProductVariation::where(['product_id' => $product->id, 'sku' => $combination['sku']])->first();
            if ($variation) {
                $variationSkus[] = $variation->sku;
            }

            $qty   = (int) ($combination['stock_qty'] ?? 0);
            $price = (float) ($combination['purchase_price'] ?? 0);
            $totalStockQty      += $qty;
            $totalPurchasePrice += $price * $qty;

            if (! $variation) {
                $variation = $this->createVariation($product, $combination, $merchantId);
                $this->createStockInventory($variation, $combination, $merchantId);
                $this->updateShopProductVariation($product, $variation, $combination, $merchantId);
            } else {
                $this->updateVariation($product, $variation, $combination, $merchantId);
            }

            if ($combination['is_default'] == 1) {
                $this->updateProductDetail($product, $variation, $combination, $merchantId);
            }

            $this->handleVariationMedia($variation, $request, $combination);
            $this->processVariationAttributes($product, $variation, $combination['attributes']);

            $index++;
        }

        return [
            'variationSkus'      => $variationSkus,
            'totalStockQty'      => $totalStockQty,
            'totalPurchasePrice' => $totalPurchasePrice,
        ];
    }

    protected function validateVariation(array $combination, Product $product): void
    {
        if (! isset($combination['sku']) || ! isset($combination['wholesale_minimum_qty']) || ! isset($combination['attributes'])) {
            throw new Exception('Missing required fields.');
        }

        $variation = ProductVariation::where(['product_id' => $product->id, 'sku' => $combination['sku']])->first();
        if ($product->productDetail->default_variation_id && $combination['is_default'] == 1 && $combination['status'] == 0) {
            throw new Exception('Status cannot be disabled.');
        }

        if (empty($combination['purchase_price'])) {
            throw new Exception('Purchase price is required.');
        }

        if (empty($combination['regular_price'])) {
            throw new Exception('Regular price is required.');
        }

        if (empty($combination['discount_price'])) {
            throw new Exception('Discount price is required.');
        }
    }

    protected function createVariation(Product $product, array $combination, $merchantId): ProductVariation
    {
        return ProductVariation::create([
            'product_id'       => $product->id,
            'sku'              => $combination['sku'],
            'barcode'          => ProductVariation::generateUnique12DigitBarcode(),
            'regular_price'    => $combination['regular_price']    ?? 0,
            'purchase_price'   => $combination['purchase_price']   ?? 0,
            'e_price'          => $combination['e_price']          ?? 0,
            'e_discount_price' => $combination['e_discount_price'] ?? 0,
            'discount_price'   => $combination['discount_price']   ?? 0,
            'wholesale_price'  => $combination['wholesale_price']  ?? 0,
            'minimum_qty'      => $combination['wholesale_minimum_qty'],
            'total_stock_qty'  => $combination['stock_qty'] ?? 0,
            'status'           => $combination['status']    ?? 0,
        ]);
    }

    protected function createStockInventory(ProductVariation $variation, array $combination, $merchantId): void
    {
        $stockInventory = StockInventory::create([
            'merchant_id'          => $merchantId,
            'product_id'           => $variation->product_id,
            'product_variation_id' => $variation->id,
            'purchase_price'       => $combination['purchase_price']   ?? 0,
            'e_price'              => $combination['e_price']          ?? 0,
            'e_discount_price'     => $combination['e_discount_price'] ?? 0,
            'regular_price'        => $combination['regular_price']    ?? 0,
            'discount_price'       => $combination['discount_price']   ?? 0,
            'wholesale_price'      => $combination['wholesale_price']  ?? 0,
            'stock_qty'            => $combination['stock_qty']        ?? 0,
        ]);

        $stockOrders = [];
        for ($i = 0; $i < ($combination['stock_qty'] ?? 0); $i++) {
            $stockOrders[] = [
                'uuid'               => (string) Str::uuid(),
                'stock_inventory_id' => $stockInventory->id,
                'purchase_price'     => $combination['purchase_price'] ?? 0,
                'created_at'         => now(),
                'updated_at'         => now(),
            ];
        }
        StockOrder::insert($stockOrders);
    }

    protected function updateShopProductVariation(Product $product, ProductVariation $variation, array $combination, $merchantId): void
    {
        if (! ShopProduct::where('product_id', $product->id)->exists()) {
            return;
        }

        $keyMap = [
            'shipping_fee_exd' => 'ed_delivery_fee',
            'shipping_fee_isd' => 'id_delivery_fee',
            'shipping_fee_osd' => 'od_delivery_fee',
        ];

        $settings     = ShopSetting::whereIn('key', array_keys($keyMap))->pluck('value', 'key');
        $shippingData = [];
        foreach ($keyMap as $dbKey => $label) {
            $shippingData[$label] = $settings[$dbKey] ?? 0;
        }

        $commission = $this->productCommissionService->calculateCommissionRate($product->id);

        $shopVariation                       = new ShopProductVariation;
        $shopVariation->product_id           = $product->id;
        $shopVariation->product_variation_id = $variation->id;
        $shopVariation->regular_price        = $combination['regular_price']    ?? 0;
        $shopVariation->e_price              = $combination['regular_price']    ?? 0;
        $shopVariation->e_discount_price     = $combination['discount_price']   ?? 0;
        $shopVariation->id_delivery_fee      = $shippingData['id_delivery_fee'] ?? 0;
        $shopVariation->od_delivery_fee      = $shippingData['od_delivery_fee'] ?? 0;
        $shopVariation->ed_delivery_fee      = $shippingData['ed_delivery_fee'] ?? 0;
        $shopVariation->packly_commission    = empty($commission['rate']) ? 0.0 : $commission['rate'];
        $shopVariation->save();

        $variation->e_price          = $shopVariation->e_price;
        $variation->e_discount_price = $shopVariation->e_discount_price;
        $variation->save();
    }

    protected function updateVariation(Product $product, ProductVariation $variation, array $combination, $merchantId): void
    {
        $stockChanged = isset($combination['stock_qty']) && $combination['stock_qty'] != $variation->total_stock_qty;
        $purchased    = PurchaseDetail::where('variation_id', $variation->id)->exists();

        if ($stockChanged && $purchased) {
            throw new Exception('Stock quantity cannot be changed.');
        }

        $variation->regular_price    = $combination['regular_price']    ?? 0;
        $variation->purchase_price   = $combination['purchase_price']   ?? 0;
        $variation->e_price          = $combination['e_price']          ?? 0;
        $variation->e_discount_price = $combination['e_discount_price'] ?? 0;
        $variation->discount_price   = $combination['discount_price']   ?? 0;
        $variation->wholesale_price  = $combination['wholesale_price']  ?? 0;
        $variation->minimum_qty      = $combination['wholesale_minimum_qty'];
        $variation->status           = $combination['status'] ?? $variation->status;
        $variation->save();

        $stockInventory = StockInventory::where(['merchant_id' => $merchantId, 'product_id' => $product->id, 'product_variation_id' => $variation->id])->first();
        $isSold         = StockOrder::where('stock_inventory_id', $stockInventory->id)->whereNotNull('sell_product_detail_id')->exists();

        if (! $isSold) {
            $stockInventory->stock_qty = $combination['stock_qty'] ?? 0;
            $stockInventory->save();

            StockOrder::where('stock_inventory_id', $stockInventory->id)->delete();
            $stockOrders = [];
            for ($i = 0; $i < ($combination['stock_qty'] ?? 0); $i++) {
                $stockOrders[] = [
                    'uuid'               => (string) Str::uuid(),
                    'stock_inventory_id' => $stockInventory->id,
                    'purchase_price'     => $combination['purchase_price'] ?? 0,
                    'created_at'         => now(),
                    'updated_at'         => now(),
                ];
            }
            StockOrder::insert($stockOrders);

            $variation->total_stock_qty = $combination['stock_qty'] ?? 0;
            $variation->save();
        }
    }

    protected function updateProductDetail(Product $product, ProductVariation $variation, array $combination, $merchantId): void
    {
        $product->productDetail->update([
            'default_variation_id' => $variation->id,
            'regular_price'        => $combination['regular_price']    ?? 0,
            'purchase_price'       => $combination['purchase_price']   ?? 0,
            'e_price'              => $combination['e_price']          ?? 0,
            'e_discount_price'     => $combination['e_discount_price'] ?? 0,
            'discount_price'       => $combination['discount_price']   ?? 0,
            'wholesale_price'      => $combination['wholesale_price']  ?? 0,
            'minimum_qty'          => $combination['wholesale_minimum_qty'],
        ]);

        $shopProductVariation = ShopProductVariation::where(['product_id' => $product->id, 'product_variation_id' => $variation->id])->first();
        if ($shopProductVariation) {
            $shopProduct = ShopProduct::where(['product_id' => $product->id, 'merchant_id' => $merchantId])->first();
            $shopProduct->update([
                'e_price'          => $shopProductVariation['e_price']          ?? 0,
                'e_discount_price' => $shopProductVariation['e_discount_price'] ?? 0,
            ]);
        }
    }

    protected function handleVariationMedia(ProductVariation $variation, Request $request, array $combination): void
    {
        $imageFile = isset($combination['variationId']) ? 'image_'.$combination['variationId'] : null;
        if ($request->hasFile($imageFile)) {
            $variation->deleteMedia();
            $variation->addMedia($request->file($imageFile), 'image');
        }
    }

    protected function processVariationAttributes(Product $product, ProductVariation $variation, array $attributes): void
    {
        // Delete old attributes to prevent mismatch/duplicates
        VariationAttribute::where('product_variation_id', $variation->id)->delete();

        // Reinsert updated attributes
        foreach ($attributes as $attribute) {

            if (! Attribute::find($attribute['attribute_id'])) {
                throw new Exception('Attribute not found.');
            }

            if (! AttributeOption::find($attribute['attribute_option_id'])) {
                throw new Exception('Attribute option not found.');
            }

            if (! AttributeOption::where([
                'id'           => $attribute['attribute_option_id'],
                'attribute_id' => $attribute['attribute_id'],
            ])->exists()) {
                throw new Exception('Attribute option does not belong to attribute.');
            }

            VariationAttribute::create([
                'product_id'           => $product->id,
                'product_variation_id' => $variation->id,
                'attribute_id'         => $attribute['attribute_id'],
                'attribute_option_id'  => $attribute['attribute_option_id'],
            ]);
        }
    }

    protected function checkExistingVariationAttribute(int $product_variation_id, int $attribute_id, int $attribute_option_id): bool
    {
        return VariationAttribute::where([
            'product_variation_id' => $product_variation_id,
            'attribute_id'         => $attribute_id,
            'attribute_option_id'  => $attribute_option_id,
        ])->exists();
    }

    protected function handleAccounting(Product $product, array $variationSkus, float $totalPurchasePrice, $merchantId, $paymentDate = null): void
    {
        $productVariationData = ProductVariation::where('product_id', $product->id)->whereIn('sku', $variationSkus)->get();
        $prePurchasePrice     = $productVariationData->sum(fn ($variation) => $variation->purchase_price * $variation->total_stock_qty);

        if ($prePurchasePrice > 0) {
            $accounts = [
                'inventory' => Account::where(['merchant_id' => $merchantId, 'account_type' => AccountTypes::INVENTORY->value, 'uucode' => 'INAS'])->first(),
                'purchases' => Account::where(['merchant_id' => $merchantId, 'account_type' => AccountTypes::PURCHASE->value, 'uucode' => 'INPU'])->first(),
                'capital'   => Account::where(['merchant_id' => $merchantId, 'account_type' => AccountTypes::EQUITY->value, 'uucode' => 'OWCA'])->first(),
            ];

            if (in_array(null, $accounts, true)) {
                throw new Exception("Missing required account(s) for merchant ID {$merchantId}.");
            }

            foreach ($accounts as $account) {
                $account->decrement('balance', $prePurchasePrice);
            }

            $uuid        = Str::uuid();
            $paymentDate = $paymentDate ?? now();

            $transactions = [
                [
                    'account' => $accounts['inventory'],
                    'type'    => 'credit',
                    'reason'  => 'Initial stock in the inventory',
                ],
                [
                    'account' => $accounts['purchases'],
                    'type'    => 'credit',
                    'reason'  => 'Initial stock in the inventory',
                ],
                [
                    'account' => $accounts['capital'],
                    'type'    => 'debit',
                    'reason'  => 'Owner capital as initial stock',
                ],
            ];

            foreach ($transactions as $txn) {
                MerchantTransaction::create([
                    'uuid'        => $uuid,
                    'merchant_id' => $merchantId,
                    'account_id'  => $txn['account']->id,
                    'amount'      => $prePurchasePrice,
                    'date'        => $paymentDate,
                    'type'        => $txn['type'],
                    'reason'      => $txn['reason'],
                ]);
            }
        }

        if ($totalPurchasePrice > 0) {
            $accounts = [
                'inventory' => Account::where(['merchant_id' => $merchantId, 'account_type' => AccountTypes::INVENTORY->value, 'uucode' => 'INAS'])->first(),
                'purchases' => Account::where(['merchant_id' => $merchantId, 'account_type' => AccountTypes::PURCHASE->value, 'uucode' => 'INPU'])->first(),
                'capital'   => Account::where(['merchant_id' => $merchantId, 'account_type' => AccountTypes::EQUITY->value, 'uucode' => 'OWCA'])->first(),
            ];

            if (in_array(null, $accounts, true)) {
                throw new Exception("Missing required account(s) for merchant ID {$merchantId}.");
            }

            foreach ($accounts as $account) {
                $account->increment('balance', $totalPurchasePrice);
            }

            $uuid        = Str::uuid();
            $paymentDate = $paymentDate ?? now();

            $transactions = [
                [
                    'account' => $accounts['inventory'],
                    'type'    => 'debit',
                    'reason'  => 'Opening stock in the inventory',
                ],
                [
                    'account' => $accounts['purchases'],
                    'type'    => 'debit',
                    'reason'  => 'Opening stock in the inventory',
                ],
                [
                    'account' => $accounts['capital'],
                    'type'    => 'credit',
                    'reason'  => 'Owner capital as opening stock',
                ],
            ];

            foreach ($transactions as $txn) {
                MerchantTransaction::create([
                    'uuid'        => $uuid,
                    'merchant_id' => $merchantId,
                    'account_id'  => $txn['account']->id,
                    'amount'      => $totalPurchasePrice,
                    'date'        => $paymentDate,
                    'type'        => $txn['type'],
                    'reason'      => $txn['reason'],
                ]);
            }
        }
    }

    protected function shouldCreateDraft(Product $product): bool
    {
        return (bool) (
            $product->shopProduct
            && in_array($product->shopProduct->status, [2, 3], true)
        );
    }

    protected function captureDraftSnapshots(Product $product): array
    {
        $product->loadMissing([
            'productDetail',
            'variations.variationAttributes.attribute',
            'variations.variationAttributes.attributeOption',
            'variations.media',
            'shopProduct',
        ]);

        return [
            'product_variant' => $this->buildVariantSnapshot($product),
            'product_pricing' => $this->buildPricingSnapshot($product),
        ];
    }

    protected function syncDraftChanges(Product $product, array $oldSnapshots): void
    {
        try {
            $newSnapshots = [
                'product_variant' => $this->buildVariantSnapshot($product),
                'product_pricing' => $this->buildPricingSnapshot($product),
            ];

            $draftChanges = [];
            $oldValues = [];

            foreach ($newSnapshots as $field => $newValue) {
                $oldValue = $oldSnapshots[$field] ?? null;

                if ($this->normalizeDraftValue($oldValue) === $this->normalizeDraftValue($newValue)) {
                    continue;
                }

                $draftChanges[$field] = $this->prepareDraftValue($newValue);
                $oldValues[$field] = $this->prepareDraftValue($oldValue);
            }

            if (empty($draftChanges)) {
                return;
            }

            $product->saveAsDraft($draftChanges, $oldValues);
            $product->update(['updated_at' => now()]);

            $oldStatus = $product->shopProduct?->status;
            $wasApproved = $oldStatus == 2;

            $product->shopProduct()?->update(['status' => 3]);

            if ($wasApproved) {
                activity()
                    ->useLog('product-update')
                    ->event('updated')
                    ->performedOn($product->shopProduct)
                    ->causedBy(auth()->user())
                    ->withProperties([
                        'old' => 'Approved',
                        'new' => 'Pending',
                    ])
                    ->log('Product status updated to pending due to merchant product variation changes');
            }
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
        }
    }

    protected function buildPricingSnapshot(Product $product): array
    {
        $detail = $product->productDetail;

        return [
            'purchase_price' => $detail?->purchase_price,
            'regular_price' => $detail?->regular_price,
            'discount_price' => $detail?->discount_price,
            'wholesale_price' => $detail?->wholesale_price,
            'minimum_qty' => $detail?->minimum_qty,
            'selling_type_id' => $detail?->selling_type_id,
            'e_price' => $detail?->e_price,
            'e_discount_price' => $detail?->e_discount_price,
        ];
    }

    protected function buildVariantSnapshot(Product $product): array
    {
        return $product->variations
            ->sortBy('sku')
            ->values()
            ->map(function ($variation) use ($product) {
                return [
                    'sku' => $variation->sku,
                    'image' => $variation->image,
                    'status' => (int) $variation->status,
                    'is_default' => (int) ($product->productDetail?->default_variation_id === $variation->id),
                    'attributes' => $variation->variationAttributes
                        ->sortBy('attribute_id')
                        ->values()
                        ->map(function ($attribute) {
                            return [
                                'attribute' => $attribute->attribute?->name,
                                'value' => $attribute->attributeOption?->attribute_value,
                            ];
                        })
                        ->toArray(),
                    'pricing' => [
                        'purchase_price' => $variation->purchase_price,
                        'regular_price' => $variation->regular_price,
                        'discount_price' => $variation->discount_price,
                        'wholesale_price' => $variation->wholesale_price,
                        'minimum_qty' => $variation->minimum_qty,
                        'e_price' => $variation->e_price,
                        'e_discount_price' => $variation->e_discount_price,
                    ],
                    'stock_qty' => $variation->total_stock_qty,
                ];
            })
            ->toArray();
    }

    protected function normalizeDraftValue(mixed $value): mixed
    {
        if ($value instanceof Collection) {
            $value = $value->toArray();
        }

        if (! is_array($value)) {
            return $value;
        }

        $keys = array_keys($value);
        $isSequential = $keys === range(0, count($keys) - 1);

        if ($isSequential) {
            return array_map(fn ($item) => $this->normalizeDraftValue($item), $value);
        }

        ksort($value);
        foreach ($value as $key => $item) {
            $value[$key] = $this->normalizeDraftValue($item);
        }

        return $value;
    }

    protected function prepareDraftValue(mixed $value): mixed
    {
        if (is_array($value)) {
            return json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }

        return $value;
    }
}
