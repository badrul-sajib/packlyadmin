<?php

namespace App\Services\Merchant\Product;

use App\Enums\AccountTypes;
use App\Models\Account\Account;
use App\Models\Merchant\MerchantTransaction;
use App\Models\Product\Product;
use App\Models\Product\ProductVariation;
use App\Models\Product\ProductDetails;
use App\Models\Stock\StockInventory;
use App\Models\Stock\StockOrder;
use App\Services\ApiResponse;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class ProductStoreService
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

    public function storeProduct($request, $merchantId): JsonResponse
    {
        try {
            DB::beginTransaction();

            $sku = $request->sku ?? $this->generateSKU($merchantId);

            // Validate product name and SKU
            $validationResult = $this->validateProduct($request, $merchantId, $sku);
            if ($validationResult instanceof JsonResponse) {
                return $validationResult;
            }

            // Generate unique slug
            $slug = $this->generateUniqueSlug($request->name);

            // Prepare and create product
            $productData = $this->prepareProductData($request, $merchantId, $sku, $slug);
            $product     = Product::create($productData);

            // Handle media (images and thumbnail)
            $this->handleMedia($request, $product);

            // Store product details
            $productDetailData = $this->prepareProductDetails($request, $product->id);
            if ($productDetailData instanceof JsonResponse) {
                return $productDetailData;
            }
            $details = ProductDetails::create($productDetailData);

            // Manage stock for single product
            if ($request->product_type_id == Product::$PRODUCT_TYPE_SINGLE && $request->has('stock_qty') && $request->stock_qty > 0) {
                $stockResult = $this->manageStock($request, $product, $details, $merchantId);
                if ($stockResult instanceof JsonResponse) {
                    return $stockResult;
                }
            }

            DB::commit();

            return ApiResponse::successMessageForCreate('Product created successfully.', $product, Response::HTTP_CREATED);
        } catch (Exception $e) {
            DB::rollBack();

            return ApiResponse::failure('Product not created.', Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function generateSKU($merchantId = null, $length = 8, $maxAttempts = 50): string
    {
        $attempts = 0;

        do {
            if ($attempts++ >= $maxAttempts) {
                throw new \RuntimeException('Could not generate unique SKU.');
            }
            $sku = Str::upper(Str::random($length));
        } while (Product::where('merchant_id', $merchantId)->where('sku', $sku)->exists());

        return $sku;
    }

    protected function validateProduct($request, $merchantId, $sku): ?JsonResponse
    {
        $existingProduct = Product::where('merchant_id', $merchantId)
            ->where(function ($query) use ($request, $sku) {
                $query->where('name', $request->name)->orWhere('sku', $sku);
            })
            ->first();

        if ($existingProduct) {
            $message = $existingProduct->name === $request->name
                ? 'A product with this name already exists for the current merchant.'
                : 'A product with this SKU already exists for the current merchant.';

            return ApiResponse::failure($message);
        }

        if (! empty($sku) && $this->skuExistsInVariations($merchantId, $sku)) {
            return ApiResponse::failure('A product variation already uses this SKU for the current merchant.');
        }

        return null;
    }

    protected function skuExistsInVariations(int $merchantId, string $sku): bool
    {
        return ProductVariation::where('sku', $sku)
            ->whereHas('product', function ($query) use ($merchantId) {
                $query->where('merchant_id', $merchantId);
            })
            ->exists();
    }

    protected function generateUniqueSlug($name): string
    {
        $baseSlug = Str::slug($name);
        $slug     = $baseSlug;
        $counter  = 1;

        while (Product::where('slug', $slug)->exists()) {
            $slug = $baseSlug.'-'.$counter;
            $counter++;
        }

        if (blank($slug)) {
            throw new \RuntimeException('Could not generate unique slug. Please change the product name.');
        }

        return $slug;
    }

    protected function prepareProductData($request, $merchantId, $sku, $slug): array|JsonResponse
    {
        $productData = [
            'merchant_id'   => $merchantId,
            'name'          => $request->name,
            'slug'          => $slug,
            'description'   => $this->decodeHTMLContent($request->description),
            'specification' => $this->decodeHTMLContent($request->specification),
            'sku'           => $sku,
            'category_id'   => $request->category_id,
            'warranty_note' => $request->warranty_note ?? null,
            'has_warranty'  => $request->has_warranty ? 1 : 0,
            'warranty_type' => $request->warranty_type,
            'weight'        => $request->weight,
            'barcode'       => Product::generateUnique12DigitBarcode(),
        ];

        if ($request->has('sub_category_id')) {
            if ($request->has('category_id')) {
                $productData['sub_category_id'] = $request->sub_category_id;
            } else {
                return ApiResponse::failure('Sub category provided without a category. Please provide a category first.', Response::HTTP_UNPROCESSABLE_ENTITY);
            }
        }

        if ($request->has('sub_category_child_id')) {
            if ($request->has('sub_category_id')) {
                $productData['sub_category_child_id'] = $request->sub_category_child_id;
            } else {
                return ApiResponse::failure('Sub category child provided without a sub category. Please provide a sub category first.', Response::HTTP_UNPROCESSABLE_ENTITY);
            }
        }

        if ($request->has('brand_id')) {
            $productData['brand_id'] = $request->brand_id;
        }

        if ($request->has('unit_id')) {
            $productData['unit_id'] = $request->unit_id;
        }

        if ($request->has('product_type_id')) {
            $productData['product_type_id'] = $request->product_type_id;
        }

        return $productData;
    }

    protected function handleMedia($request, $product): void
    {
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $product->addMedia($image, 'images');
            }
        }

        if ($request->hasFile('thumbnail')) {
            $product->addMedia($request->thumbnail, 'thumbnail');
        }
    }

    protected function prepareProductDetails($request, $productId): array|JsonResponse
    {
        $productDetailData = [
            'product_id'      => $productId,
            'selling_type_id' => $request->selling_type_id,
        ];

        if ($request->product_type_id == Product::$PRODUCT_TYPE_SINGLE) {
            if ($request->has('purchase_price')) {
                $productDetailData['purchase_price'] = $request->purchase_price ?? 0;
            }
            if ($request->has('regular_price')) {
                $productDetailData['regular_price'] = $request->regular_price;
            }
            if ($request->has('e_price')) {
                $productDetailData['e_price'] = $request->e_price;
            }
            if ($request->has('e_discount_price')) {
                $productDetailData['e_discount_price'] = $request->e_discount_price;
            }
            if ($request->has('discount_price')) {
                $productDetailData['discount_price'] = $request->discount_price;
            }
            if ($request->selling_type_id == Product::$SELLING_TYPE_WHOLESALE || $request->selling_type_id == Product::$SELLING_TYPE_BOTH) {
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

    protected function manageStock($request, $product, $details, $merchantId): ?JsonResponse
    {
        $stockData = [
            'merchant_id'      => $merchantId,
            'product_id'       => $product->id,
            'purchase_price'   => $details->purchase_price,
            'e_price'          => $details->e_price          ?? 0,
            'e_discount_price' => $details->e_discount_price ?? 0,
            'regular_price'    => $details->regular_price    ?? 0,
            'discount_price'   => $details->discount_price   ?? 0,
            'wholesale_price'  => $details->wholesale_price  ?? 0,
            'stock_qty'        => $request->stock_qty,
        ];

        $product->increment('total_stock_qty', $request->stock_qty);
        $stockInventory = StockInventory::create($stockData);

        $stockOrders = [];
        for ($i = 0; $i < $request->stock_qty; $i++) {
            $stockOrders[] = [
                'uuid'               => (string) Str::uuid(),
                'stock_inventory_id' => $stockInventory->id,
                'purchase_price'     => $details->purchase_price ?? 0,
                'created_at'         => now(),
                'updated_at'         => now(),
            ];
        }
        StockOrder::insert($stockOrders);

        $uuid        = Str::uuid();
        $paymentDate = $request->payment_date ?? now();
        $amount      = $request->stock_qty * $details->purchase_price;

        $accounts = [
            'purchases' => Account::where(['merchant_id' => $merchantId, 'account_type' => AccountTypes::PURCHASE->value, 'uucode' => 'INPU'])->first(),
            'inventory' => Account::where(['merchant_id' => $merchantId, 'account_type' => AccountTypes::INVENTORY->value, 'uucode' => 'INAS'])->first(),
            'capital'   => Account::where(['merchant_id' => $merchantId, 'account_type' => AccountTypes::EQUITY->value, 'uucode' => 'OWCA'])->first(),
        ];

        if (in_array(null, $accounts, true)) {
            throw new Exception('One or more required accounts could not be found.');
        }

        foreach ($accounts as $account) {
            $account->increment('balance', $amount);
        }

        $transactions = [
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
            [
                'account' => $accounts['inventory'],
                'type'    => 'credit',
                'reason'  => 'Owner capital as opening stock',
            ],
        ];

        foreach ($transactions as $txn) {
            MerchantTransaction::create([
                'uuid'        => $uuid,
                'merchant_id' => $merchantId,
                'account_id'  => $txn['account']->id,
                'amount'      => $amount,
                'date'        => $paymentDate,
                'type'        => $txn['type'],
                'reason'      => $txn['reason'],
            ]);
        }

        return null;
    }
}
