<?php

namespace App\Services;

use App\Caches\ProductListingCache;
use App\Enums\ShopProductStatus;
use App\Exceptions\ShopProductStatusException;
use App\Jobs\LogSearchQuery;
use App\Models\Merchant\Merchant;
use App\Models\Merchant\MerchantReport;
use App\Models\Order\Order;
use App\Models\Product\Product;
use App\Models\Shop\ShopProduct;
use App\Models\Variation\VariationAttribute;
use App\Support\CursorRescue;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class ProductService
{
    public function getNewShopProducts($request)
    {
        try {
            $queryString = http_build_query($request->query());
            $perPage = $request->input('per_page', 50);
            $isCounted = $request->input('is_counted', 0);
            $isCursor = $request->input('is-cursor', 0);

            $filters = [
                \App\Filters\Product\BestMatchFilter::class,
                \App\Filters\Product\BrandFilter::class,
                \App\Filters\Product\CategoryFilter::class,
                \App\Filters\Product\SubCategoryFilter::class,
                \App\Filters\Product\ChildCategoryFilter::class,
                \App\Filters\Product\DiscountFilter::class,
                \App\Filters\Product\MerchantFilter::class,
                \App\Filters\Product\PriceRangeFilter::class,
                \App\Filters\Product\PriceSortFilter::class,
                \App\Filters\Product\PrimeViewFilter::class,
                \App\Filters\Product\RatingFilter::class,
                \App\Filters\Product\RelatedProductFilter::class,
                \App\Filters\Product\SearchFilter::class,
            ];

            if ($request->search) {
                $perPage = 100;
            }

            return (new ProductListingCache)->get($queryString, function () use ($filters, $perPage, $isCounted, $isCursor) {

                $baseShopQuery = Product::baseShopQuery();
                $query = app(Pipeline::class)
                    ->send($baseShopQuery)
                    ->through($filters)
                    ->via('handle')
                    ->thenReturn();

                $query->orderBy('products.id');

                if ($isCursor) {
                    $products = CursorRescue::run(function () use ($query, $perPage) {
                        return $query->cursorPaginate($perPage);
                    });
                } else {
                    $products = $query->paginate($perPage);
                }

                if ($isCounted == '1' && $isCursor) {
                    $products->total_count = $query->count();
                } else {
                    $products->total_count = null;
                }

                if (request()->search && $products->isEmpty()) {

                    $randomProducts = Product::baseShopQuery()->orderBy('products.id');

                    if ($isCursor) {

                        $products = CursorRescue::run(function () use ($randomProducts, $perPage) {
                            return $randomProducts->cursorPaginate($perPage);
                        });
                    } else {
                        $products = $randomProducts->paginate($perPage);
                    }

                    if ($isCounted == '1' && $isCursor) {
                        $products->total_count = $randomProducts->count();
                    } else {
                        $products->total_count = null;
                    }

                    $products->is_random = true;
                }

                return $products;
            });
        } catch (Exception $e) {
            report($e);

            throw $e;
        }
    }

    public function getNewShopProductDetails($slug)
    {
        return Product::where('slug', $slug)
            ->where('status', 1)
            ->whereHas('shopProduct', function ($query) {
                $query->where('status', 2)->where('active_status', 1);
            })
            ->select('id', 'weight', 'name', 'sku', 'product_type_id', 'category_id', 'sub_category_id', 'sub_category_child_id', 'brand_id', 'slug', 'description', 'specification', 'merchant_id', 'total_stock_qty', 'warranty_note', 'has_warranty', 'warranty_type')
            ->with([
                'productDetail:id,product_id,regular_price,discount_price,default_variation_id,e_price,e_discount_price,id_delivery_fee,od_delivery_fee,ed_delivery_fee',
                'productDetail.selectedVariation:id,sku,regular_price,discount_price,id_delivery_fee,od_delivery_fee,ed_delivery_fee',
                'variations.variationAttributes.attribute',
                'variations.variationAttributes.attributeOption',
                'merchant.followers',
                'merchant:id,shop_name,slug',
                'brand',
            ])
            ->first();
    }

    public static function getProductBySlug($slug)
    {
        return Product::where('slug', $slug)
            ->select(
                'id',
                'name',
                'sku',
                'barcode',
                'product_type_id',
                'category_id',
                'sub_category_id',
                'sub_category_child_id',
                'brand_id',
                'unit_id',
                'slug',
                'description',
                'specification',
                'merchant_id',
                'total_stock_qty',
                'warranty_note',
                'has_warranty',
                'warranty_type',
                'weight'
            )
            ->with([
                'productDetail:id,product_id,purchase_price,regular_price,discount_price,wholesale_price,minimum_qty,selling_type_id',
                'variations.variationAttributes.attribute',
                'variations.variationAttributes.attributeOption',
                'merchant:id,shop_name',
                'brand',
                'shopProduct',
            ])
            ->firstOrFail();
    }

    public static function getProductVariantBySlug($slug): JsonResponse
    {
        try {
            // Eager load all necessary relationships in one query
            $product = Product::with([
                'media',
                'variations.media',
                'variations.stockInventory', // Load stockInventory with variations
                'variations.variationAttributes.attribute',
                'variations.variationAttributes.attributeOption',
            ])->where('slug', $slug)->first();

            if (! $product) {
                return failure('Product not found', Response::HTTP_NOT_FOUND);
            }

            // Filter variations with stock > 0 after loading
            $variationsWithStock = $product->variations->filter(function ($variation) {
                return $variation->stockInventory && $variation->stockInventory->stock_qty > 0;
            });

            return success('Product variations fetched successfully', [
                'id' => (int) $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'attributes' => self::getAttributes($variationsWithStock),
                'variations' => self::getVariations($variationsWithStock),
            ]);
        } catch (Exception $e) {
            return failure('Something went wrong', 500);
        }
    }

    public static function requestProducts(Request $request): LengthAwarePaginator|array
    {
        $perPage = $request->input('perPage', 10);
        $page = $request->input('page', 1);
        $merchant_id = $request->input('merchant_id', '');
        $search = $request->input('search', '');

        return ShopProduct::query()
            ->with([
                'merchant:id,name,shop_status,shop_name,phone',
                'product:id,name,slug,total_stock_qty',
                'product.media',
            ])
            ->whereHas('product', function ($query) {
                $query->whereDoesntHave('draft');
            })
            ->where('status', 1)
            ->when($merchant_id, function ($query) use ($merchant_id) {
                return $query->where('merchant_id', $merchant_id);
            })
            ->when($search, function ($query) use ($search) {
                return $query->where(function ($query) use ($search) {
                    $query->whereHas('product', function ($query) use ($search) {
                        return $query->where('name', 'like', '%' . $search . '%');
                    })->orWhereHas('merchant', function ($query) use ($search) {
                        return $query->whereAny(['name', 'phone', 'shop_name'], 'like', "%{$search}%");
                    });
                });
            })
            ->orderBy('status', 'asc')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);
    }

    public static function requestProductsForMerchant(Request $request): LengthAwarePaginator|array
    {
        $perPage = $request->input('perPage', 10);
        $page = $request->input('page', 1);
        $status = $request->input('status', '');
        $merchant_id = $request->input('merchant_id', '');
        $search = $request->input('search', '');

        return ShopProduct::query()
            ->with([
                'merchant:id,name,shop_status,shop_name,phone',
                'product:id,name,slug,total_stock_qty',
                'product.media',
            ])
            ->when($status, function ($query) use ($status) {
                return $query->where('status', $status);
            })
            ->when($merchant_id, function ($query) use ($merchant_id) {
                return $query->where('merchant_id', $merchant_id);
            })
            ->when($search, function ($query) use ($search, $status) {
                return $query->where(function ($query) use ($search, $status) {
                    $query->whereHas('product', function ($query) use ($search) {
                        return $query->where('name', 'like', '%' . $search . '%');
                    })->orWhereHas('merchant', function ($query) use ($search) {
                        return $query->whereAny(['name', 'phone', 'shop_name'], 'like', "%{$search}%");
                    });
                    if ($status) {
                        $query->where('status', $status);
                    }
                });
            })
            ->orderBy('status', 'asc')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);
    }

    public static function inventoryProducts(Request $request): LengthAwarePaginator
    {
        $perPage = $request->input('perPage', 10);
        $page = $request->input('page', 1);
        $merchant_id = $request->input('merchant_id', '');
        $search = $request->input('search', '');

        return Product::query()
            ->with([
                'media',
                'productDetail',
                'category:id,name',
                'merchant:id,name,shop_status,shop_name,phone',
            ])
            ->whereDoesntHave('shopProduct')
            ->when($search, function ($query) use ($search) {
                $clean = strtolower(trim(preg_replace('/\s+/', ' ', $search)));
                $query->whereRaw("LOWER(REGEXP_REPLACE(name, '[[:space:]]+', ' ')) LIKE ?", ["%{$clean}%"])
                    ->orWhere('sku', 'like', "%{$clean}%");
            })
            ->when($merchant_id, function ($query) use ($merchant_id) {
                $query->where('merchant_id', $merchant_id);
            })
            ->paginate($perPage, ['*'], 'page', $page);
    }

    public static function requestProductStatus($request)
    {
        $product = ShopProduct::find($request->id);

        if (! $product) {
            throw new ModelNotFoundException('Product not found');
        }

        $mainProduct = Product::find($product->product_id);

        if ($mainProduct && $mainProduct->draft && $mainProduct->draft->status == 'pending') {
            throw new ShopProductStatusException('Product status cannot be updated because some fields are already pending for change');
        }

        $oldStatus = ShopProductStatus::label()[$product->status];
        $newStatus = ShopProductStatus::label()[$request->status];
        $product->status = $request->status;
        $product->active_status = $request->status == ShopProductStatus::REJECTED->value ? 0 : 1;
        $product->save();

        $statusLabel = ShopProductStatus::label()[$request->status];

        $productName = $product->product->name;

        if (ShopProductStatus::REJECTED->value == $request->status && $request->reject_reason) {

            // Send SMS to merchant
            $message = "Dear {$product->merchant->name}, your product \"{$productName}\" has been rejected. Reason: {$request->reject_reason}. Please contact support for more details.";
            MerchantReport::create([
                'merchant_id' => $product->merchant_id,
                'report_details' => $message,
                'status' => 'Pending',
                'added_by' => Auth::user()->id,
            ]);

            $product->merchant->sendNotification(
                'Product Rejected',
                'Product "' . $productName . '" has been rejected. Click to see details.',
                '/notice'
            );
        } else {
            $product->merchant->sendNotification(
                'Product Status Updated',
                'Product "' . $productName . '" status updated to ' . $statusLabel . ' by admin',
                '/shop-product'
            );
        }

        activity()
            ->useLog('product-status-update')
            ->event('updated')
            ->performedOn($product)
            ->causedBy(auth()->user())
            ->withProperties([
                'old' => $oldStatus,
                'new' => $newStatus,
                'note' => $request->reject_reason ?? null,
            ])
            ->log('Product status updated by ' . auth()->user()->name);

        return $product;
    }

    public static function bulkProductStatus(array $data)
    {
        if (empty($data['product_ids']) || count($data['product_ids']) === 0) {
            return;
        }

        // Get products before update for logging and notifications
        $products = ShopProduct::with(['product', 'merchant', 'product.draft'])
            ->whereIn('id', $data['product_ids'])
            ->get();

        if ($products->isEmpty()) {
            return;
        }

        foreach ($products as $product) {
            if ($product->product && $product->product->draft && $product->product->draft->status == 'pending') {
                throw new ShopProductStatusException('Product status cannot be updated because some fields are already pending for change');
            }
        }

        // Perform bulk status update
        ShopProduct::whereIn('id', $data['product_ids'])->update([
            'status' => $data['status'],
            'active_status' => $data['status'] == ShopProductStatus::REJECTED->value ? 0 : 1,
        ]);

        // Handle notifications and logging for each product
        foreach ($products as $product) {
            $oldStatus = ShopProductStatus::label()[$product->status];
            $newStatus = ShopProductStatus::label()[$data['status']];
            $productName = $product->product->name;
            $statusLabel = ShopProductStatus::label()[$data['status']];

            // Handle rejected products with reason
            if (ShopProductStatus::REJECTED->value == $data['status'] && isset($data['reject_reason'])) {
                // Send rejection message to merchant
                $message = "Dear {$product->merchant->name}, your product \"{$productName}\" has been rejected. Reason: {$data['reject_reason']}. Please contact support for more details.";

                // Create merchant report
                MerchantReport::create([
                    'merchant_id' => $product->merchant_id,
                    'report_details' => $message,
                    'status' => 'Pending',
                    'added_by' => Auth::user()->id,
                ]);

                // Send notification to merchant
                $product->merchant->sendNotification(
                    'Product Rejected',
                    'Product "' . $productName . '" has been rejected. Click to see details.',
                    '/notice'
                );
            } else {
                // Send status update notification
                $product->merchant->sendNotification(
                    'Product Status Updated',
                    'Product "' . $productName . '" status updated to ' . $statusLabel . ' by admin',
                    '/shop-product'
                );
            }

            // Log activity for each product
            activity()
                ->useLog('product-status-update')
                ->event('updated')
                ->performedOn($product)
                ->causedBy(auth()->user())
                ->withProperties([
                    'old' => $oldStatus,
                    'new' => $newStatus,
                    'bulk_operation' => true,
                    'affected_products_count' => count($data['product_ids']),
                ])
                ->log('Product status updated by ' . auth()->user()->name);
        }

        // Return updated products count for confirmation
        return true;
    }

    public static function getShopProducts(Request $request): LengthAwarePaginator|array
    {
        $perPage = $request->input('perPage', 10);
        $page = $request->input('page', 1);
        $search = $request->input('search', '');
        $merchant_id = $request->input('merchant_id', '');
        $stock_status = $request->input('stock_status', '');

        return ShopProduct::query()
            ->with([
                'merchant:id,name,shop_status,shop_name,phone',
                'product',
                'product.productDetail:id,product_id,regular_price,discount_price',
                'product.media',
                'product.category:id,name',
            ])->active()
            ->when($search, function ($query) use ($search) {
                $clean = strtolower(trim(preg_replace('/\s+/', ' ', $search)));

                $query->whereHas('product', function ($query) use ($clean) {
                    $query->whereRaw("LOWER(REGEXP_REPLACE(name, '[[:space:]]+', ' ')) LIKE ?", ["%{$clean}%"])
                        ->orWhere('sku', 'like', "%{$clean}%");
                });
            })
            ->when($merchant_id, function ($query) use ($merchant_id) {
                return $query->where('merchant_id', $merchant_id);
            })
            ->when($stock_status, function ($query) use ($stock_status) {
                $query->whereHas('product', function ($query) use ($stock_status) {
                    if ($stock_status == '1') {
                        return $query->where('total_stock_qty', '>', 0);
                    }
                    if ($stock_status == '2') {
                        return $query->where('total_stock_qty', 0);
                    }
                    if ($stock_status == '3') {
                        return $query->where('total_stock_qty', '<', 10);
                    }
                });
            })
            ->paginate($perPage, ['*'], 'page', $page);
    }

    public static function getAllProducts(Request $request): LengthAwarePaginator|array
    {
        $perPage = $request->input('perPage', 10);
        $page = $request->input('page', 1);
        $status = $request->input('status', '');
        $merchant_id = $request->input('merchant_id', '');
        $search = $request->input('search', '');

        return ShopProduct::query()
            ->with([
                'merchant:id,name,shop_status,shop_name,phone',
                'product',
                'product.productDetail:id,product_id,regular_price,discount_price',
                'product.media',
                'product.category:id,name',
            ])
            ->when($status, function ($query) use ($status) {
                return $query->where('status', $status);
            })
            ->when($merchant_id, function ($query) use ($merchant_id) {
                return $query->where('merchant_id', $merchant_id);
            })
            ->when($search, function ($query) use ($search, $status) {
                return $query->where(function ($query) use ($search, $status) {
                    $query->whereHas('product', function ($query) use ($search) {
                        return $query->where('name', 'like', '%' . $search . '%');
                    })->orWhereHas('merchant', function ($query) use ($search) {
                        return $query->whereAny(['name', 'phone', 'shop_name'], 'like', "%{$search}%");
                    });

                    if ($status) {
                        $query->where('status', $status);
                    }
                });
            })
            ->orderBy('status', 'asc')
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'asc')
            ->paginate($perPage, ['*'], 'page', $page);
    }

    public static function getShopLimitProducts($data, $limit = 30)
    {
        $search = $data['search'] ?? '';
        $primeViewId = $data['prime_view_id'] ?? null;

        $alreadySelectedProductIds = collect();

        if ($primeViewId) {
            $alreadySelectedProductIds = DB::table('prime_view_product')
                ->where('prime_view_id', $primeViewId)
                ->pluck('product_id');
        }

        return ShopProduct::query()
            ->active()
            ->with('product:id,name', 'product.media', 'product.reviews')
            ->when($search, function ($query) use ($search) {
                $query->whereHas('product', function ($query) use ($search) {
                    return $query->where('name', 'like', '%' . $search . '%');
                });
            })
            ->limit($limit)->get()
            ->map(function ($shopProduct) use ($alreadySelectedProductIds) {
                $product = $shopProduct->product;

                $shopProduct->product->already_selected = $alreadySelectedProductIds->contains($product->id);

                return $shopProduct;
            });
    }

    public static function getShopProductsForBadge($search, $limit = 30)
    {
        $merchant_id = request()->input('merchant_id', '');

        return ShopProduct::query()
            ->active()
            ->activeStatus()
            ->with('product:id,name', 'product.media', 'product.reviews')
            ->when($search, function ($query) use ($search) {
                $query->whereHas('product', function ($query) use ($search) {
                    return $query->where('name', 'like', '%' . $search . '%');
                });
            })
            ->when($merchant_id, function ($query) use ($merchant_id) {
                return $query->where('merchant_id', $merchant_id);
            })
            ->limit($limit)->get();
    }

    public static function getProductSuggestions($request)
    {
        try {
            $search = strtolower($request->search ?? '');
            $limit = is_numeric($request->limit) && $request->limit > 0 ? min($request->limit, 100) : 20;
            $results = (new OpenSearchService)->search('products', $search);
            $hits = $results['hits']['hits'] ?? [];
            $formatted = array_map(fn($hit) => $hit['_source'], $hits);

            return collect($formatted)->take($limit)->select('id', 'name')->map(function ($data) {
                return [
                    'id' => intval($data['id']),
                    'name' => $data['name'],
                    'slug' => '',
                ];
            })->toArray();
        } catch (\Throwable $th) {
            Log::error('Failed to search products in OpenSearch: ' . $th->getMessage());

            return [];
        }
    }

    public static function getProductKeywordSuggestions($request)
    {
        try {
            $search = strtolower($request->search ?? '');
            $limit = is_numeric($request->limit) && $request->limit > 0 ? min($request->limit, 100) : 20;
            $results = (new OpenSearchService)->search('products', $search);
            $hits = $results['hits']['hits'] ?? [];
            $formatted = array_map(fn($hit) => $hit['_source'], $hits);

            return collect($formatted)->take($limit)->pluck('name')->toArray();
        } catch (\Throwable $th) {
            Log::error('Failed to search products in OpenSearch: ' . $e->getMessage());

            return [];
        }
    }

    public static function getMerchantCategoryProducts($request): Collection|array
    {
        $merchantIds = $request->merchant_ids ?? [];
        $categoryIds = $request->category_ids ?? [];
        $brandIds = $request->brand_ids ?? [];
        $merchantType = $request->merchant_type ?? '';
        $categoryType = $request->category_type ?? '';
        $brandType = $request->brand_type ?? '';
        $searchType = $request->search_type ?? 'coupon';

        $query = Product::where('status', 1)
            ->with('media');

        if ($searchType == 'coupon') {
            $query->whereHas('shopProduct', function ($query) {
                $query->where('status', 2);
            });
        }

        if ($merchantType == '1' && $merchantIds) {
            $query->whereNotIn('merchant_id', $merchantIds);
        }
        if ($merchantType == '2' && $merchantIds) {
            if (is_array($merchantIds)) {
                $query->whereIn('merchant_id', $merchantIds);
            } else {
                $query->where('merchant_id', $merchantIds);
            }
        }
        if ($categoryType == '1' && $categoryIds) {
            $query->whereNotIn('category_id', $categoryIds);
        }
        if ($categoryType == '2' && $categoryIds) {
            if (is_array($categoryIds)) {
                $query->whereIn('category_id', $categoryIds);
            } else {
                $query->where('category_id', $categoryIds);
            }
        }
        if ($brandType == '2' && $categoryIds) {
            $query->whereIn('brand_ids', $brandIds);
        }
        if ($brandType == '1' && $categoryIds) {
            $query->whereNotIn('brand_ids', $brandIds);
        }

        return $query->limit(20)->select('id', 'name', 'slug')->get();
    }

    // ----------------------all support methods----------------------
    public static function getAttributes($variationsWithStock): array
    {
        // Get all variation attribute IDs at once
        $variationIds = $variationsWithStock->pluck('id')->toArray();

        // Query all needed data in one go
        return VariationAttribute::with('attributeOption', 'attribute', 'variation.media')
            ->whereIn('product_variation_id', $variationIds)
            ->orderBy('attribute_id')
            ->get()
            ->unique('attribute_option_id')
            ->map(function ($variationAttribute) {
                return [
                    'id' => (int) $variationAttribute->attribute_id,
                    'name' => $variationAttribute->attribute->name,
                    'value' => $variationAttribute->attributeOption->attribute_value,
                    'valueId' => (int) $variationAttribute->attributeOption->id,
                    'image' => $variationAttribute->variation->image,
                ];
            })
            ->values() // Reset array keys
            ->toArray();
    }

    public static function getVariations($variations)
    {
        return $variations->map(function ($variation) {
            $stockInventory = $variation->stockInventory;

            return [
                'id' => (int) $variation->id,
                'sku' => $variation->sku,
                'regular_price' => $stockInventory->regular_price,
                'discount_price' => $stockInventory->discount_price,
                'quantity' => $stockInventory->stock_qty,
                'image' => $variation->image,
                'variant' => $variation->variationAttributes->map(function ($va) {
                    return [
                        'attribute_id' => (int) $va->attribute->id,
                        'attribute_name' => $va->attribute->name,
                        'attribute_option' => $va->attributeOption->attribute_value,
                        'attribute_option_id' => (int) $va->attributeOption->id,
                    ];
                }),
            ];
        });
    }

    public function getNewArrivals($request)
    {
        try {
            $perPage = $request->input('perPage', 10);

            $query = Product::where('status', 1)
                ->whereHas('shopProduct', function ($query) {
                    $query->where('status', 2);
                })
                ->with(['productDetail:id,product_id,regular_price,discount_price'])
                ->leftJoin('product_details', 'products.id', '=', 'product_details.product_id')
                ->select(
                    'products.id',
                    'products.name',
                    'products.slug',
                    'products.product_type_id',
                    'product_details.regular_price',
                    'product_details.discount_price'
                )
                ->orderBy('products.created_at', 'desc');

            return $query->paginate($perPage);
        } catch (Exception $e) {
            return failure('An error occurred while fetching new arrivals', 500);
        }
    }

    public function getBestSellings($request)
    {
        try {
            $perPage = $request->input('perPage', 10);

            $query = Product::where('status', 1)
                ->whereHas('shopProduct', function ($query) {
                    $query->where('status', 2);
                })
                ->with(['productDetail:id,product_id,regular_price,discount_price'])
                ->leftJoin('product_details', 'products.id', '=', 'product_details.product_id')
                ->leftJoin('order_items', function ($join) {
                    $join->on('products.id', '=', 'order_items.product_id')
                        ->where('order_items.status_id', 4);
                })
                ->select(
                    'products.id',
                    'products.name',
                    'products.slug',
                    'products.product_type_id',
                    'product_details.regular_price',
                    'product_details.discount_price',
                    DB::raw('COUNT(order_items.id) as orders_count')
                )
                ->groupBy(
                    'products.id',
                    'products.name',
                    'products.slug',
                    'products.product_type_id',
                    'product_details.regular_price',
                    'product_details.discount_price'
                )
                ->having('orders_count', '>', 0)
                ->orderBy('orders_count', 'desc');

            return $query->paginate($perPage);
        } catch (Exception $e) {
            return failure('Something went wrong', 500);
        }
    }

    /**
     * @throws Exception
     */
    public function getShopForMe($request)
    {
        $user = $request->user();
        $queryString = http_build_query($request->query());
        $perPage = $request->input('perPage', 10);
        $is_counted = $request->input('is_counted', 0);
        $isCursor = $request->input('is-cursor', 0);
        $totalCount = null;

        $lastOrders = Order::when($user, function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })
            ->when(! $user, function ($query) {
                $query->take(10);
            })
            ->with('orderItems.product')
            ->orderBy('created_at', 'desc')
            ->get();

        $categoryIds = [];
        $orderedProductIds = [];

        foreach ($lastOrders as $order) {
            foreach ($order->orderItems as $orderItem) {
                $product = $orderItem->product;
                $orderedProductIds[] = $product->id;
                $categoryIds[] = $product->category_id;
            }
        }

        if (empty($orderedProductIds)) {
            $products = Product::inRandomOrder()->limit(10)->select('id', 'category_id')->get();
            foreach ($products as $product) {
                $orderedProductIds[] = $product->id;
                $categoryIds[] = $product->category_id;
            }
        }

        $categoryIds = array_unique($categoryIds);

        if (! $user) {
            $orderedProductIds = [];
        }

        return (new ProductListingCache)->get($queryString, function () use (&$totalCount, $categoryIds, $orderedProductIds, $perPage, $is_counted, $isCursor) {

            $baseQuery = Product::baseShopQuery()
                ->when($categoryIds, fn($q) => $q->whereIn('products.category_id', $categoryIds))
                ->when($orderedProductIds, fn($q) => $q->whereNotIn('products.id', $orderedProductIds))
                ->distinct('products.id')
                ->orderBy('products.id');

            if ($is_counted == '1') {
                $totalCount = $baseQuery->count();
            }
            if ($isCursor) {
                $products = CursorRescue::run(function () use ($baseQuery, $perPage) {
                    return $baseQuery->cursorPaginate($perPage);
                });
            } else {
                $products = $baseQuery->paginate($perPage);
            }
            $products->total_count = $totalCount;

            return $products;
        });
    }

    /**
     * @throws Exception
     */
    public function getForYouProducts($merchantId, $request)
    {
        $merchant = Merchant::find($merchantId);
        if (empty($merchant)) {
            throw new ModelNotFoundException('Merchant not found');
        }
        request()->merge(['merchant_id' => $merchantId]);
        request()->merge(['sort' => 'low_price']);

        return $this->getNewShopProducts($request);
    }

    public static function updateProduct(array $data, $product): void
    {
        $oldData = $product->only(array_keys($data));

        if (array_key_exists('description', $data)) {
            $data['description'] = self::decodeHTMLContent($data['description']);
        }

        if (array_key_exists('specification', $data)) {
            $data['specification'] = self::decodeHTMLContent($data['specification']);
        }

        $product->update($data);

        $newData = $product->only(array_keys($data));

        $changes = [];
        foreach ($oldData as $field => $oldValue) {
            if ($oldValue != $newData[$field]) {
                $changes[$field] = [
                    'old' => $oldValue,
                    'new' => $newData[$field],
                ];
            }
        }

        if (empty($changes)) {
            return;
        }
        activity()
            ->useLog('product-update')
            ->event('updated')
            ->performedOn($product)
            ->causedBy(auth()->user())
            ->withProperties($changes)
            ->log('Product updated by ' . auth()->user()->name);
    }

    private static function decodeHTMLContent($encodedHtml)
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

    private function logSearchAsync($search, $userId): void
    {
        dispatch(new LogSearchQuery($search, $userId))
            ->onQueue('low');
    }
}
