<?php

namespace App\Services\Merchant\Product;

use App\Enums\AccountTypes;
use App\Enums\ShopProductStatus;
use App\Models\Account\Account;
use App\Models\Merchant\MerchantTransaction;
use App\Models\Product\Product;
use App\Models\Product\ProductWarranty;
use App\Models\Product\ShopProduct;
use App\Models\Purchase\PurchaseDetail;
use App\Models\Stock\StockInventory;
use App\Models\Stock\StockOrder;
use App\Models\Stock\StockTransferDetail;
use App\Services\ApiResponse;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class ProductUpdateService
{
    protected function decodeHTMLContent($encodedHtml)
    {
        if (empty($encodedHtml)) {
            return '';
        }

        // Decode from base64
        $decoded = base64_decode($encodedHtml);

        // Try to decode as URI component first (fallback method)
        if ($uriDecoded = urldecode($decoded)) {
            // Check if this was likely encoded with encodeURIComponent
            if (strpos($uriDecoded, '%') === false && $uriDecoded !== $decoded) {
                return $uriDecoded;
            }
        }

        // If URI decoding didn't work, try as binary string (modern method)
        return $decoded;
    }

    public function updateProduct($request, $slug, $merchantId): JsonResponse
    {
        try {

            $product = Product::where(['slug' => $slug, 'merchant_id' => $merchantId])->firstOrFail();
            $productDetail = $product->productDetail;
            $shouldCreateDraft = $this->shouldCreateDraft($product);
            $draftSnapshots = $shouldCreateDraft ? $this->captureDraftSnapshots($product) : [];



            DB::beginTransaction();

            // Validate product name uniqueness
            $existingProduct = Product::where('merchant_id', $merchantId)
                ->where(function ($query) use ($request, $product) {
                    $query->where('name', $request->name)->where('id', '!=', $product->id);
                })
                ->first();

            if (! empty($existingProduct)) {
                return ApiResponse::validationError('Product name already exists', [], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            // Prepare product data
            $productData = [
                'name' => $request->name,
                'slug' => $this->generateUniqueSlug($request->name, $product->id),
                'description' => $this->decodeHTMLContent($request->description),
                'specification' => $this->decodeHTMLContent($request->specification),
                'sku' => $request->sku,
                'category_id' => $request->category_id,
                'warranty_note' => $request->warranty_note ?? null,
                'has_warranty' => $request->has_warranty ? 1 : 0,
                'warranty_type' => $request->warranty_type,
            ];

            // Validate category and sub-category relationships
            if ($request->has('sub_category_id')) {
                if ($request->has('category_id')) {
                    $productData['sub_category_id'] = $request->sub_category_id;
                } else {
                    return ApiResponse::failure('Sub category provided without a category. Please provide a category first.', Response::HTTP_UNPROCESSABLE_ENTITY);
                }
            } else {
                $productData['sub_category_id'] = null;
            }

            if ($request->has('sub_category_child_id')) {
                if ($request->has('sub_category_id')) {
                    $productData['sub_category_child_id'] = $request->sub_category_child_id;
                } else {
                    return ApiResponse::failure('Sub category child provided without a sub category. Please provide a sub category first.', Response::HTTP_UNPROCESSABLE_ENTITY);
                }
            } else {
                $productData['sub_category_child_id'] = null;
            }

            // Add optional fields
            if ($request->has('brand_id')) {
                $productData['brand_id'] = $request->brand_id;
            } else {
                $productData['brand_id'] = null;
            }

            if ($request->has('unit_id')) {
                $productData['unit_id'] = $request->unit_id;
            } else {
                $productData['unit_id'] = null;
            }

            if ($request->has('product_type_id')) {
                $productData['product_type_id'] = $request->product_type_id;
            }

            // Handle product type change
            if ($request->filled('product_type_id') && $request->product_type_id != $product->product_type_id) {
                $validationResult = $this->validateProductTypeChange($product, $merchantId, $request);
                if ($validationResult instanceof JsonResponse) {
                    return $validationResult;
                }

                $detailData = $this->handleProductTypeChange($product, $request, $merchantId);
                $productDetail->update($detailData);
                $productData['total_stock_qty'] = 0;
            }
            if ($request->has('weight')) {
                $productData['weight'] = $request->weight;
            }
            // Update product
            $product->update($productData);

            // Handle single product details
            if ($product->product_type_id == Product::$PRODUCT_TYPE_SINGLE) {
                $productDetailData = $this->prepareSingleProductDetails($request, $product);
                if ($productDetailData instanceof JsonResponse) {
                    return $productDetailData;
                }
                $productDetail->update($productDetailData);
            }

            // Handle thumbnail
            if ($request->hasFile('thumbnail')) {
                $product->thumbnail = $request->file('thumbnail');
                $product->save();
            }

            // Handle image removal
            if ($request->has('remove_images')) {
                $this->removeImages($product, $request->remove_images);
            }

            // Handle new images
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $product->addMedia($image, 'images');
                }
            }

            if ($shouldCreateDraft) {
                $this->syncDraftChanges($product->fresh(['productDetail', 'variations.variationAttributes.attribute', 'variations.variationAttributes.attributeOption', 'variations.media']), $request, $draftSnapshots);
            }

            // Handle stock for single product
            if ($product->product_type_id == Product::$PRODUCT_TYPE_SINGLE && $request->has('stock_qty') && $request->stock_qty >= 0 && $request->stock_qty != $product->total_stock_qty) {
                $stockResult = $this->updateStock($product, $productDetail, $request, $merchantId);
                if ($stockResult instanceof JsonResponse) {
                    return $stockResult;
                }
            }

            DB::commit();

            return ApiResponse::success('Product updated successfully.', $product, Response::HTTP_OK);
        } catch (Exception $e) {
            DB::rollBack();

            return ApiResponse::failure('Product not found.', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    protected function generateUniqueSlug($name, $id): string
    {
        $baseSlug = Str::slug($name);
        $slug = $baseSlug;
        $counter = 1;

        while (Product::where('slug', $slug)->where('id', '!=', $id)->exists()) {
            $slug = $baseSlug.'-'.$counter;
            $counter++;
        }

        return $slug;
    }

    protected function validateProductTypeChange($product, $merchantId, $request): ?JsonResponse
    {
        if (PurchaseDetail::where('product_id', $product->id)->exists()) {
            return ApiResponse::failure('You cannot change the product type if the product has been purchased from supplier.', Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        if ($request->has('stock_qty') && $request->stock_qty != $product->total_stock_qty && PurchaseDetail::where('product_id', $product->id)->exists()) {
            return ApiResponse::failure('You cannot change the stock quantity if the product has been purchased from supplier.', Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $shopProduct = ShopProduct::where([
            'merchant_id' => $merchantId,
            'product_id' => $product->id,
        ])->first();

        if ($shopProduct) {
            return ApiResponse::failure('Product type cannot be changed. Product is currently listed in E-commerce.', Response::HTTP_CONFLICT);
        }

        $stockInventory = StockInventory::query()
            ->where('merchant_id', $merchantId)
            ->where('product_id', $product->id);
        $hasExistingStock = $stockInventory->whereHas('stockOrders', function ($query) {
            $query->whereNotNull('sell_product_detail_id');
        })->exists();
        if ($hasExistingStock) {
            return ApiResponse::failure(
                'Product type cannot be changed. Product has existing stock.',
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        $hasStockTransfer = StockTransferDetail::whereHas('stockTransfer', function ($query) use ($merchantId) {
            $query->where('merchant_id', $merchantId);
        })->where('product_id', $product->id)->exists();

        if ($hasStockTransfer) {
            return ApiResponse::failure(
                'Product type cannot be changed. Product has existing stock transfer records.',
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        return null;
    }

    protected function handleProductTypeChange($product, $request, $merchantId): array
    {
        $detailData = [];

        // Handle accounting for stock deletion
        $stockInventory = StockInventory::query()
            ->where('merchant_id', $merchantId)
            ->where('product_id', $product->id);

        $deletedStockPrice = 0;
        foreach ($stockInventory->get() as $stockInventoryItem) {
            $deletedStockPrice += ($stockInventoryItem->purchase_price * $stockInventoryItem->stock_qty);
        }

        $uuid = Str::uuid();
        $paymentDate = $request->payment_date ?? now();
        $amount = $deletedStockPrice;

        $accounts = [
            'purchases' => Account::where([
                'merchant_id' => $merchantId,
                'account_type' => AccountTypes::PURCHASE->value,
                'uucode' => 'INPU',
            ])->first(),

            'inventory' => Account::where([
                'merchant_id' => $merchantId,
                'account_type' => AccountTypes::INVENTORY->value,
                'uucode' => 'INAS',
            ])->first(),

            'capital' => Account::where([
                'merchant_id' => $merchantId,
                'account_type' => AccountTypes::EQUITY->value,
                'uucode' => 'OWCA',
            ])->first(),
        ];

        if (in_array(null, $accounts, true)) {
            throw new Exception('One or more required accounts could not be found.');
        }

        foreach ($accounts as $account) {
            $account->decrement('balance', $amount);
        }

        $transactions = [
            [
                'account' => $accounts['purchases'],
                'type' => 'credit',
                'reason' => 'Opening stock in the inventory',
            ],
            [
                'account' => $accounts['capital'],
                'type' => 'debit',
                'reason' => 'Owner capital as opening stock',
            ],
            [
                'account' => $accounts['inventory'],
                'type' => 'debit',
                'reason' => 'Owner capital as opening stock',
            ],
        ];

        foreach ($transactions as $txn) {
            MerchantTransaction::create([
                'uuid' => $uuid,
                'merchant_id' => $merchantId,
                'account_id' => $txn['account']->id,
                'amount' => -$amount,
                'date' => $paymentDate,
                'type' => $txn['type'],
                'reason' => $txn['reason'],
            ]);
        }

        foreach ($product->stockInventories as $inventory) {
            $inventory->stockOrders()->delete();
            $inventory->delete();
        }

        // Handle product type change
        if ($product->product_type_id == Product::$PRODUCT_TYPE_VARIANT) {
            $product->variationAttributes()->delete();
            $product->variations()->delete();
            $detailData['default_variation_id'] = null;
        } else {
            $detailData['purchase_price'] = 0;
            $detailData['regular_price'] = 0;
            $detailData['discount_price'] = 0;
            $detailData['wholesale_price'] = 0;
            $detailData['e_price'] = 0;
            $detailData['e_discount_price'] = 0;
        }

        return $detailData;
    }

    protected function prepareSingleProductDetails($request, Product $product): array|JsonResponse
    {
        $productDetailData = [];

        if ($request->has('purchase_price')) {
            $productDetailData['purchase_price'] = $request->purchase_price;
        }
        if ($request->has('e_price')) {
            $productDetailData['e_price'] = $request->e_price;
        }
        if ($request->has('e_discount_price')) {
            $productDetailData['e_discount_price'] = $request->e_discount_price;
        }
        if ($request->has('regular_price')) {
            $productDetailData['regular_price'] = $request->regular_price;
        }
        if ($request->has('discount_price')) {
            $productDetailData['discount_price'] = $request->discount_price;
        }
        $productDetailData['selling_type_id'] = $request->selling_type_id;

        if ($request->selling_type_id == Product::$SELLING_TYPE_WHOLESALE && $product->product_type_id == Product::$SELLING_TYPE_BOTH) {
            if (empty($request->minimum_qty)) {
                return ApiResponse::failure('Minimum quantity is required.');
            }

            if ($request->has('wholesale_price')) {
                $productDetailData['wholesale_price'] = $request->wholesale_price;
            }
            if ($request->has('minimum_qty')) {
                $productDetailData['minimum_qty'] = $request->minimum_qty;
            }
        }

        if ($request->has('is_enable_accounting') && $request->is_enable_accounting == 1) {
            if ($request->has('purchase_account_id')) {
                $productDetailData['purchase_account_id'] = $request->input('purchase_account_id');
            }
            if ($request->has('inventory_account_id')) {
                $productDetailData['inventory_account_id'] = $request->input('inventory_account_id');
            }
            if ($request->has('sale_account_id')) {
                $productDetailData['sale_account_id'] = $request->input('sale_account_id');
            }
        }

        return $productDetailData;
    }

    protected function removeImages($product, $remove_images): void
    {
        $ids = [];
        $remove_images = json_decode($remove_images);

        foreach ($remove_images as $image) {
            $images = $product->getMedia('images');
            if (empty($images[$image])) {
                continue;
            }
            $imageToDelete = $images[$image];
            $ids[] = $imageToDelete['id'];
        }

        foreach ($ids as $id) {
            $product->deleteMedia($id);
        }
    }

    protected function updateStock($product, $productDetail, $request, $merchantId): ?JsonResponse
    {
        $stockInventories = $product->stockInventories()->whereNull('purchase_id')->get();
        $stockOrders = StockOrder::whereIn('stock_inventory_id', $stockInventories->pluck('id'))->whereNotNull('sell_product_detail_id')->get();

        if ($stockInventories->isNotEmpty() && $stockOrders->isNotEmpty()) {
            return ApiResponse::failure('You cannot add stock to this product. It is in use.', Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $diffQty = $request->stock_qty - $product->total_stock_qty;
        $stockOrders->each(function ($stockOrder) {
            $stockOrder->delete();
        });
        $stockInventories->each(function ($stockInventory) {
            $stockInventory->delete();
        });
        $product->decrement('total_stock_qty', $product->total_stock_qty);

        $stockData = [
            'merchant_id' => $merchantId,
            'product_id' => $product->id,
            'purchase_price' => $productDetail->purchase_price,
            'e_price' => $productDetail->e_price ?? 0,
            'e_discount_price' => $productDetail->e_discount_price ?? 0,
            'regular_price' => $productDetail->regular_price ?? 0,
            'discount_price' => $productDetail->discount_price ?? 0,
            'wholesale_price' => $productDetail->wholesale_price ?? 0,
            'stock_qty' => $request->stock_qty,
        ];

        $product->increment('total_stock_qty', $request->stock_qty ?? 0);

        $stockInventory = $request->stock_qty > 0 ? StockInventory::create($stockData) : null;

        $stockOrders = [];
        for ($i = 0; $i < $request->stock_qty; $i++) {
            $stockOrders[] = [
                'uuid' => (string) Str::uuid(),
                'stock_inventory_id' => $stockInventory->id,
                'purchase_price' => $productDetail->purchase_price ?? 0,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        StockOrder::insert($stockOrders);

        if ($diffQty !== 0) {
            $uuid = Str::uuid();
            $paymentDate = $request->payment_date ?? now();
            $amount = abs($diffQty) * $productDetail->purchase_price;
            $isPositive = $diffQty > 0;

            $accounts = [
                'purchases' => Account::where([
                    'merchant_id' => $merchantId,
                    'account_type' => AccountTypes::PURCHASE->value,
                    'uucode' => 'INPU',
                ])->first(),

                'inventory' => Account::where([
                    'merchant_id' => $merchantId,
                    'account_type' => AccountTypes::INVENTORY->value,
                    'uucode' => 'INAS',
                ])->first(),

                'capital' => Account::where([
                    'merchant_id' => $merchantId,
                    'account_type' => AccountTypes::EQUITY->value,
                    'uucode' => 'OWCA',
                ])->first(),
            ];

            if (in_array(null, $accounts, true)) {
                throw new Exception('One or more required accounts could not be found.');
            }

            foreach ($accounts as $account) {
                $isPositive ? $account->increment('balance', $amount) : $account->decrement('balance', $amount);
            }

            $transactions = [
                [
                    'account' => $accounts['purchases'],
                    'type' => 'debit',
                    'reason' => 'Opening stock in the inventory',
                ],
                [
                    'account' => $accounts['capital'],
                    'type' => 'credit',
                    'reason' => 'Owner capital as opening stock',
                ],
                [
                    'account' => $accounts['inventory'],
                    'type' => 'credit',
                    'reason' => 'Owner capital as opening stock',
                ],
            ];

            foreach ($transactions as $txn) {
                MerchantTransaction::create([
                    'uuid' => $uuid,
                    'merchant_id' => $merchantId,
                    'account_id' => $txn['account']->id,
                    'amount' => $isPositive ? $amount : -$amount,
                    'date' => $paymentDate,
                    'type' => $txn['type'],
                    'reason' => $txn['reason'],
                ]);
            }
        }

        return null;
    }

    protected function shouldCreateDraft(Product $product): bool
    {
        return (bool) (
            $product->shopProduct
            && in_array($product->shopProduct->status, [
                ShopProductStatus::APPROVED->value,
                ShopProductStatus::PENDING->value,
                ShopProductStatus::REJECTED->value,
                ShopProductStatus::DISSABLED->value,
            ])
        );
    }

    protected function captureDraftSnapshots(Product $product): array
    {
        $product->loadMissing([
            'category:id,name',
            'subCategory:id,name',
            'subCategoryChild:id,name',
            'productDetail',
            'variations.variationAttributes.attribute',
            'variations.variationAttributes.attributeOption',
            'variations.media',
            'shopProduct',
        ]);

        return [
            'product_name' => $product->name,
            'product_category' => $this->buildCategorySnapshot($product),
            'product_weight' => $product->weight,
            'product_pricing' => $this->buildPricingSnapshot($product),
            'product_warranty' => $this->buildWarrantySnapshot($product),
            'product_image' => $this->buildImageSnapshot($product),
            'product_variant' => $this->buildVariantSnapshot($product),
        ];
    }

    protected function syncDraftChanges(Product $product, Request $request, array $oldSnapshots): void
    {
        try {
            $newSnapshots = [
                'product_name' => $product->name,
                'product_category' => $this->buildCategorySnapshot($product),
                'product_weight' => $product->weight,
                'product_warranty' => $this->buildWarrantySnapshot($product),
                'product_pricing' => $this->buildPricingSnapshot($product),
                'product_image' => $this->buildImageSnapshot($product),
                'product_variant' => $this->buildVariantSnapshot($product),
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
            $wasApproved = $oldStatus == ShopProductStatus::APPROVED->value;

            $product->shopProduct()?->update(['status' => ShopProductStatus::PENDING->value]);

            if ($wasApproved) {
                activity()
                    ->useLog('product-update')
                    ->event('updated')
                    ->performedOn($product->shopProduct)
                    ->causedBy(auth()->user())
                    ->withProperties([
                        'old' => ShopProductStatus::label()[$oldStatus],
                        'new' => ShopProductStatus::label()[ShopProductStatus::PENDING->value],
                    ])
                    ->log('Product status updated to pending due to merchant product changes');
            }
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
        }
    }

    protected function buildWarrantySnapshot(Product $product): array
    {
        return [
            'has_warranty' => (int) ($product->has_warranty ?? 0),
            'warranty_type' => $product->warranty_type,
            'warranty_note' => $product->warranty_note,
        ];
    }

    protected function buildCategorySnapshot(Product $product): array
    {
        return [
            'category' => $product->category?->name,
            'sub_category' => $product->subCategory?->name,
            'child_category' => $product->subCategoryChild?->name,
        ];
    }

    protected function buildPricingSnapshot(Product $product): array
    {
        $detail = $product->productDetail;

        return [
            'purchase_price' => $detail?->purchase_price,
            'regular_price' => $detail?->regular_price,
            'discount_price' => $detail?->discount_price,
            'e_price' => $detail?->e_price,
            'e_discount_price' => $detail?->e_discount_price,
        ];
    }

    protected function buildImageSnapshot(Product $product): array
    {
        return [
            'thumbnail' => $product->thumbnail,
            'gallery' => array_values($product->getUrl('images', config('app.url')) ?? []),
        ];
    }

    protected function buildVariantSnapshot(Product $product): array
    {
        if ((int) $product->product_type_id !== Product::$PRODUCT_TYPE_VARIANT) {
            return [];
        }

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
