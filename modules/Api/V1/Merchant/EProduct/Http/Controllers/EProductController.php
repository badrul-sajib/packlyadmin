<?php

namespace Modules\Api\V1\Merchant\EProduct\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use App\Enums\MerchantStatus;
use App\Services\ApiResponse;
use App\Jobs\PushNotification;
use App\Models\Product\Product;
use App\Enums\ShopProductStatus;
use App\Traits\ProductCommission;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Models\Product\ShopProduct;
use App\Models\Setting\ShopSetting;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Product\ProductDetails;
use App\Models\Product\ProductVariation;
use Illuminate\Support\Facades\Validator;
use App\Models\PrimeView\PrimeViewProduct;
use App\Services\ProductCommissionService;
use App\Models\Product\ShopProductVariation;
use Symfony\Component\HttpFoundation\Response;
use Modules\Api\V1\Merchant\EProduct\Http\Requests\EProductRequest;
use Modules\Api\V1\Merchant\EProduct\Http\Resources\EProductResource;
use Modules\Api\V1\Merchant\EProduct\Http\Requests\ProductDisableRequest;
use Modules\Api\V1\Merchant\EProduct\Http\Resources\EProductDetailsResource;
use Modules\Api\V1\Merchant\EProduct\Http\Requests\ProductIdValidationRequest;

class EProductController extends Controller
{
    use ProductCommission;
    public function __construct()
    {
        $this->middleware('shop.permission:show-shop-products')->only('index', 'search');
        $this->middleware('shop.permission:create-and-update-shop-products')->only('store');
        $this->middleware('shop.permission:update-shop-products-status')->only('status', 'bulkStatus');
    }

    /**
     * Lists all e-products.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = $request->query('per_page', 10);

            $eProductQuery = ShopProduct::where('merchant_id', auth()->user()->merchant?->id)
                ->with([
                    'product.category',
                    'product.variationAttributes.attribute.options',
                    'shopProductVariations',
                    'shopProductVariations.variation.media',
                ])
                ->when($request->has('search'), function ($query) use ($request) {
                    $query->whereHas('product', function ($productQuery) use ($request) {
                        $productQuery->where('name', 'LIKE', "%{$request->search}%")
                            ->orWhere('sku', 'LIKE', "%{$request->search}%")
                            ->orWhereHas('category', function ($categoryQuery) use ($request) {

                                $categoryQuery->where('name', 'LIKE', "%{$request->search}%");
                            });
                    });
                })
                ->when($request->has('status'), function ($query) use ($request) {
                    if ($request->status == 0) {
                        $query->where('status', ShopProductStatus::DISSABLED->value);
                    } elseif ($request->status == 1) {
                        $query->where('status', ShopProductStatus::APPROVED->value);
                    } elseif ($request->status == 2) {
                        $query->where('status', ShopProductStatus::PENDING->value);
                    } elseif ($request->status == 4) {
                        $query->where('status', ShopProductStatus::REJECTED->value);
                    }
                })
                ->when($request->has('type'), function ($query) use ($request) {
                    $query->whereHas('product', function ($productQuery) use ($request) {
                        $productQuery->where('product_type_id', $request->type);
                    });
                })
                ->when($request->has(['start_date', 'end_date']), function ($query) use ($request) {
                    if ($request->start_date === $request->end_date) {
                        $query->whereDate('created_at', $request->start_date);
                    } else {
                        $query->whereBetween('created_at', [$request->start_date, $request->end_date]);
                    }
                });

            $eProducts = $eProductQuery->orderBy('id', 'desc')->paginate($perPage);

            return ApiResponse::formatPagination('E-Products retrieved successfully', $eProducts, Response::HTTP_OK);
        } catch (Exception $e) {
            return ApiResponse::failure('Something went wrong', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function shopProducts(Request $request): JsonResponse
    {
        try {
            $perPage = $request->query('per_page', 10);
            $sort_order = $request->query('sort_order', 'desc');
            $categoryId = $request->query('category_id');
            $subCategoryId = $request->query('sub_category_id');
            $subCategoryChildId = $request->query('sub_category_child_id');
            $query = Product::where('merchant_id', auth()->user()->merchant?->id);
            $query->with([
                'media',
                'category',
                'shopProduct:id,product_id,status,regular_price,e_price,e_discount_price,created_at',
                'productDetail:id,product_id,default_variation_id',
                'productDetail.selectedVariation:id,sku,total_stock_qty',
            ])
                ->whereHas('shopProduct')
                ->when($request->has('search'), function ($query) use ($request) {
                    $query->where(function ($q) use ($request) {
                        $q->where('name', 'LIKE', "%{$request->search}%")
                            ->orWhere('sku', 'LIKE', "%{$request->search}%")
                            ->orWhereHas('category', function ($categoryQuery) use ($request) {
                                $categoryQuery->where('name', 'LIKE', "%{$request->search}%");
                            });
                    });
                })
                ->when($request->has('status'), function ($query) use ($request) {
                    $query->whereHas('shopProduct', function ($subQuery) use ($request) {
                        if ($request->status == 0) {
                            $subQuery->where('status', ShopProductStatus::DISSABLED->value);
                        } elseif ($request->status == 1) {
                            $subQuery->where('status', ShopProductStatus::APPROVED->value);
                        } elseif ($request->status == 2) {
                            $subQuery->where('status', ShopProductStatus::PENDING->value);
                        } elseif ($request->status == 4) {
                            $subQuery->where('status', ShopProductStatus::REJECTED->value);
                        }
                    });
                })
                ->when($request->has('type'), function ($query) use ($request) {
                    $query->where('product_type_id', $request->type);
                })
                ->when($request->has(['start_date', 'end_date']), function ($query) use ($request) {
                    $query->whereHas('shopProduct', function ($subQuery) use ($request) {
                        if ($request->start_date === $request->end_date) {
                            $subQuery->whereDate('created_at', $request->start_date);
                        } else {
                            $subQuery->whereBetween('created_at', [$request->start_date, $request->end_date]);
                        }
                    });
                })
                ->when($categoryId, function ($q) use ($categoryId) {
                    $q->where('category_id', $categoryId);
                })
                ->when($subCategoryId, function ($q) use ($subCategoryId) {
                    $q->where('sub_category_id', $subCategoryId);
                })
                ->when($subCategoryChildId, function ($q) use ($subCategoryChildId) {
                    $q->where('sub_category_child_id', $subCategoryChildId);
                });

            $products = $query->orderBy('id', $sort_order)->paginate($perPage);

            return response()->json([
                'message'       => 'E-Products retrieved successfully',
                'items'         => EProductResource::collection($products),
                'total'         => $products->total(),
                'last_page'     => $products->lastPage(),
                'current_page'  => $products->currentPage(),
                'next_page_url' => $products->nextPageUrl(),
            ], Response::HTTP_OK, [], JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            return ApiResponse::failure('Something went wrong' . $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function shopProductDetails($id): JsonResponse
    {
        try {
            $product = ShopProduct::with([
                'product.variationAttributes',
                'product.variationAttributes.attribute.options',
                'shopProductVariations',
                'shopProductVariations.variation.media',
            ])->find($id);

            return success('Product retrieved successfully', new EProductDetailsResource($product), Response::HTTP_OK);
        } catch (Exception $e) {
            return ApiResponse::failure('Something went wrong', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Creates a new e-product.
     */
    public function store(EProductRequest $request): JsonResponse
    {

        $request->validated();

        try {
            DB::beginTransaction();

            $shopSettings           = ShopSetting::where('key', 'maximum_product_request')->first();
            $merchant               = Auth::user()->merchant;

            if ($merchant->configuration) {
                $maxProductRequestLimit = $merchant->configuration->maximum_product_request;
            } else {
                $maxProductRequestLimit = $shopSettings ? $shopSettings->value : 20;
            }
            $isNewProduct = count($request->products ?? []) > 0;

            if ($isNewProduct) {
                $activeProductsCount = ShopProduct::where('merchant_id', $merchant->id)
                    ->where('status', 2) // Assuming 2 is for active status
                    ->count();

                if ($activeProductsCount >= $maxProductRequestLimit) {
                    return ApiResponse::failure(
                        "You have reached the maximum number of active product requests ({$maxProductRequestLimit}).",
                        Response::HTTP_UNPROCESSABLE_ENTITY
                    );
                }
            }
            if ($request->updates && count($request->updates) > 0) {

                foreach ($request->updates as $update) {
                    if ($update['e_price'] === null || $update['e_price'] === '' || $update['e_price'] === '0' || $update['e_price'] === '0.00') {
                        return ApiResponse::failure('Regular price is required', Response::HTTP_UNPROCESSABLE_ENTITY);
                    }
                    if ($update['e_discount_price'] === null || $update['e_discount_price'] === '' || $update['e_discount_price'] === '0' || $update['e_discount_price'] === '0.00') {
                        return ApiResponse::failure('Discount price is required', Response::HTTP_UNPROCESSABLE_ENTITY);
                    }
                }
            }
            // Process new product requests
            $this->processNewProducts($request->products ?? [], $merchant);

            // Process updates
            $this->processProductUpdates($request->updates ?? [], $isNewProduct);

            $notificationMessage = $merchant->shop_name . ' has requested a product.';

            DB::commit();

            try {
                if (! empty($request->products)) {
                    // check merchant status
                    if (Auth::user()->merchant->shop_status != MerchantStatus::Active) {

                        $newMerchantNotificationMessage = 'One of your new merchants is waiting for activation.';

                        PushNotification::dispatch([
                            'title'      => 'New Merchant Activation Request',
                            'message'    => $newMerchantNotificationMessage,
                            'type'       => 'info',
                            'action_url' => '/merchants/' . Auth::user()->merchant->id,
                        ]);
                    }

                    PushNotification::dispatch([
                        'title'      => 'New Product Shop Request',
                        'message'    => $notificationMessage,
                        'type'       => 'info',
                        'action_url' => '/request/products',
                    ]);
                }
            } catch (Exception $e) {
                Log::error($e->getMessage());
            }

            return ApiResponse::success('Product Shop Request created.', [], Response::HTTP_OK);
        } catch (Exception $e) {
            DB::rollBack();

            return ApiResponse::failure('Something went wrong', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    protected function processProductUpdates(array $updates, $isNewProduct)
    {
        foreach ($updates as $update) {
            $product = Product::with('variations', 'productDetail')->find($update['product_id']);

            if (! $product) {
                return ApiResponse::failure('Product not found', Response::HTTP_NOT_FOUND);
            }

            $shopProduct = ShopProduct::where('product_id', $product->id)->first();

            $product->update([
                'updated_at' => now(), // Update product last updated time
            ]);

            if ($product->product_type_id == 1) {

                // Simple product
                $details                   = ProductDetails::find($product->productDetail->id);
                $details->e_discount_price = $update['e_discount_price'] ?? 0;
                $details->e_price          = $update['e_price']          ?? 0;
                $details->id_delivery_fee  = $update['id_delivery_fee']  ?? 0;
                $details->od_delivery_fee  = $update['od_delivery_fee']  ?? 0;
                $details->ed_delivery_fee  = $update['ed_delivery_fee']  ?? 0;
                $details->save();

                // check if exist on ShopProduct
                if ($shopProduct) {
                    if ($shopProduct->e_price != $update['e_price'] && ! $isNewProduct) {
                        $product->saveAsDraft([
                            'e_price'           => $update['e_price'],
                        ], [
                            'e_price'           => $shopProduct->e_price,
                        ]);

                        $shopProduct->status = ShopProductStatus::PENDING->value;
                    }
                    if ($shopProduct->e_discount_price != $update['e_discount_price'] && ! $isNewProduct) {
                        $product->saveAsDraft([
                            'e_discount_price'  => $update['e_discount_price'],
                        ], [
                            'e_discount_price'  => $shopProduct->e_discount_price,
                        ]);

                        $shopProduct->status = ShopProductStatus::PENDING->value;
                    }

                    $shopProduct->e_discount_price = $update['e_discount_price'] ?? 0;
                    $shopProduct->e_price          = $update['e_price']          ?? 0;
                    $shopProduct->id_delivery_fee  = $update['id_delivery_fee']  ?? 0;
                    $shopProduct->od_delivery_fee  = $update['od_delivery_fee']  ?? 0;
                    $shopProduct->ed_delivery_fee  = $update['ed_delivery_fee']  ?? 0;
                    $shopProduct->save();
                }
            } else {

                // Variable product
                $variation                   = ProductVariation::find($update['product_variation_id']);
                $variation->e_discount_price = $update['e_discount_price'] ?? 0;
                $variation->e_price          = $update['e_price']          ?? 0;
                $variation->id_delivery_fee  = $update['id_delivery_fee']  ?? 0;
                $variation->od_delivery_fee  = $update['od_delivery_fee']  ?? 0;
                $variation->ed_delivery_fee  = $update['ed_delivery_fee']  ?? 0;
                $variation->save();

                $productDetail = ProductDetails::where('product_id', $product->id)->where('default_variation_id', $update['product_variation_id'])->first();

                if ($productDetail && $productDetail->default_variation_id == $update['product_variation_id']) {

                    if ($shopProduct) {
                        $shopProduct->e_discount_price = $update['e_discount_price'] ?? 0;
                        $shopProduct->e_price          = $update['e_price']          ?? 0;
                        $shopProduct->id_delivery_fee  = $update['id_delivery_fee']  ?? 0;
                        $shopProduct->od_delivery_fee  = $update['od_delivery_fee']  ?? 0;
                        $shopProduct->ed_delivery_fee  = $update['ed_delivery_fee']  ?? 0;
                        $shopProduct->save();
                    }
                }

                // check if exist on ShopProduct
                $shopProductVariation = ShopProductVariation::where('product_id', $product->id)
                    ->where('product_variation_id', $variation->id)
                    ->first();

                \Log::info($product->id . ' - ' . $update['product_variation_id'] . ' - ' . $shopProductVariation);

                if ($shopProductVariation) {

                    if ($shopProductVariation->e_price != $update['e_price'] && ! $isNewProduct) {
                        $product->saveAsDraft([
                            'sku-' . $shopProductVariation->variation->sku . ' price'           => $update['e_price'],
                        ], [
                            'sku-' . $shopProductVariation->variation->sku . ' price'           => $shopProductVariation->e_price,
                        ]);
                        $shopProduct->status = ShopProductStatus::PENDING->value;
                    }
                    if ($shopProductVariation->e_discount_price != $update['e_discount_price'] && ! $isNewProduct) {
                        $product->saveAsDraft([
                            'sku-' . $shopProductVariation->variation->sku . ' discount_price'  => $update['e_discount_price'],
                        ], [
                            'sku-' . $shopProductVariation->variation->sku . ' discount_price'  => $shopProductVariation->e_discount_price,
                        ]);
                        $shopProduct->status = ShopProductStatus::PENDING->value;
                    }

                    $shopProduct->save();

                    $shopProductVariation->e_discount_price = $update['e_discount_price'] ?? 0;
                    $shopProductVariation->e_price          = $update['e_price']          ?? 0;
                    $shopProductVariation->id_delivery_fee  = $update['id_delivery_fee']  ?? 0;
                    $shopProductVariation->od_delivery_fee  = $update['od_delivery_fee']  ?? 0;
                    $shopProductVariation->ed_delivery_fee  = $update['ed_delivery_fee']  ?? 0;
                    $shopProductVariation->save();
                }
            }
        }
    }

    protected function processNewProducts(array $products, $merchant)
    {
        $requestedProducts = [];

        foreach ($products as $productData) {
            $productId = $productData['product_id'];

            // Skip duplicates
            if (in_array($productId, $requestedProducts)) {
                continue;
            }

            // Validate product ID using rules from request class
            Validator::make(
                ['product_id' => $productId],
                (new ProductIdValidationRequest)->rules()
            );

            // Check if product already exists
            if (ShopProduct::where('product_id', $productId)->exists()) {
                return ApiResponse::failure('One of the products is already requested.', Response::HTTP_TOO_MANY_REQUESTS);
            }

            // Create new shop product
            $product = Product::with('variations', 'productDetail')->find($productId);
            $this->createShopProduct($product, $merchant);

            // Create variations if needed
            if ($product->product_type_id == 2) {
                $this->createShopProductVariations($product);
            }

            $requestedProducts[] = $productId;
        }
    }

    protected function createShopProduct($product, $merchant): ShopProduct
    {
        $commission = (new ProductCommissionService)->calculateCommissionRate($product->id);
        // load commission and set
        $shopProduct               = new ShopProduct;
        $shopProduct->product_id   = $product->id;
        $shopProduct->product_type = $product->product_type_id;
        $shopProduct->merchant_id  = $merchant->id;
        $shopProduct->status       = $merchant->auto_approve == 1
            ? ShopProductStatus::APPROVED->value
            : ShopProductStatus::PENDING->value;

        if ($product->product_type_id == 1) {
            $shopProduct->regular_price     = $product->productDetail->regular_price;
            $shopProduct->e_price           = $product->productDetail->regular_price;
            $shopProduct->e_discount_price  = $product->productDetail->e_discount_price;
            $shopProduct->id_delivery_fee   = $product->productDetail->id_delivery_fee;
            $shopProduct->od_delivery_fee   = $product->productDetail->od_delivery_fee;
            $shopProduct->ed_delivery_fee   = $product->productDetail->ed_delivery_fee;
            $shopProduct->packly_commission = empty($commission['rate']) ? 0.0 : $commission['rate'];
        }

        $shopProduct->save();

        return $shopProduct;
    }

    protected function createShopProductVariations($product): void
    {
        $commission = (new ProductCommissionService)->calculateCommissionRate($product->id);
        // load commission and set
        foreach ($product->variations as $variation) {
            $shopVariation                       = new ShopProductVariation;
            $shopVariation->product_id           = $product->id;
            $shopVariation->product_variation_id = $variation->id;
            $shopVariation->regular_price        = $product->productDetail->regular_price;
            $shopVariation->e_price              = $variation->e_price;
            $shopVariation->e_discount_price     = $variation->e_discount_price;
            $shopVariation->id_delivery_fee      = $product->productDetail->id_delivery_fee;
            $shopVariation->od_delivery_fee      = $product->productDetail->od_delivery_fee;
            $shopVariation->ed_delivery_fee      = $product->productDetail->ed_delivery_fee;
            $shopVariation->packly_commission    = empty($commission['rate']) ? 0.0 : $commission['rate'];
            $shopVariation->save();
        }
    }

    /*
     * Searches e-products by keyword.
     */
    public function search(Request $request): JsonResponse
    {
        try {
            $search             = $request->input('search');
            $productId          = $request->input('product_id');
            $perPage            = $request->query('per_page', 10);
            $categoryId         = $request->query('category_id');
            $subCategoryId      = $request->query('sub_category_id');
            $ChildCategoryId    = $request->query('child_category_id');
            $productType        = $request->query('product_type');
            $sortOrder          = $request->query('sort_order', 'desc');
            $type               = $request->query('type');

            $query = Product::where('merchant_id', auth()->user()->merchant?->id)
                ->with([
                    'category',
                    'variations.media',
                    'productDetail',
                    'media',
                    'merchantCommission',
                    'variations' => function ($query) {
                        $query->where('status', 1)
                            ->with(['variationAttributes.attribute.options']);
                    }
                ])
                ->withCount(['variations' => function ($query) {
                    $query->where('status', 1);
                }])
                ->when($productId, function ($query) use ($productId) {
                    $query->where('id', $productId);
                })
                ->where(function ($query) use ($search) {
                    if ($search) {
                        $query->where('name', 'LIKE', "%{$search}%")
                            ->orWhere('sku', 'LIKE', "%{$search}%");
                    }
                })
                ->where('status', 1)
                ->where('total_stock_qty', '>', 0)
                ->when($type == 'shop_product', function ($query) {
                    $query->whereHas('shopProducts', function ($query) {
                        $query->where('status', ShopProductStatus::APPROVED->value)->where('active_status', 1);
                    });
                }, function ($query) {
                    $query->whereDoesntHave('shopProducts');
                });

            if ($categoryId) {
                $query->where('category_id', $categoryId);
            }

            if ($subCategoryId) {
                $query->where('sub_category_id', $subCategoryId);
            }

            if ($ChildCategoryId) {
                $query->where('sub_category_child_id', $ChildCategoryId);
            }

            if (in_array($productType, [1, 2])) {
                $query->where('product_type', $productType);
            }

            if (! in_array($sortOrder, ['asc', 'desc'])) {
                $sortOrder = 'desc';
            }

            $results = $query->orderBy('id', $sortOrder)->paginate($perPage);

            $merchant = Auth::user()->merchant;

            // Format the variations using map
            $formattedResults = $results->through(function ($product) use ($merchant) {

                $commissionData           = $this->getProductCommission($product);
                $product->commission      = $commissionData->commission;
                $product->commission_type = $commissionData->commission_type;

                $product->variations = $product->variations->map(function ($variation) {

                    // Format variation attributes
                    $variation->attributes = $variation->variationAttributes->map(function ($attribute) {

                        return [
                            'name'  => $attribute->attribute->name,
                            'value' => $attribute->attribute->options->firstWhere('id', $attribute->attribute_option_id)->attribute_value ?? null,
                        ];
                    });

                    unset(
                        $variation->variationAttributes
                    );

                    return $variation;
                });

                return $product;
            });

            return ApiResponse::formatPagination('Search results', $formattedResults, Response::HTTP_OK);
        } catch (Exception $e) {
            return ApiResponse::failure('Something went wrong', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /*
     * Changes the status of an e-product.
     */
    public function status(int $id): JsonResponse
    {
        try {
            $EProduct = ShopProduct::where('merchant_id', auth()->user()->merchant->id)
                ->where('id', $id)
                ->firstOrFail();

            $oldStatus              = ShopProductStatus::label()[$EProduct->status];

            if (
                $EProduct->status    == ShopProductStatus::PENDING->value
                || $EProduct->status == ShopProductStatus::REJECTED->value
                || $EProduct->status == ShopProductStatus::DISSABLED->value
            ) {
                return ApiResponse::failure('You can\'t enable this Product', Response::HTTP_FORBIDDEN);
            }
            if (
                $EProduct->status    == ShopProductStatus::APPROVED->value
            ) {
                return ApiResponse::failure('You can\'t disable this Product', Response::HTTP_FORBIDDEN);
            }

            if ($EProduct->product?->merchant?->shop_status == MerchantStatus::Suspended) {
                return ApiResponse::failure('Your shop is Inactive. You can\'t change product status.', Response::HTTP_FORBIDDEN);
            }

            if (PrimeViewProduct::where('product_id', $EProduct->product_id)->where('status', 'active')->exists() && $EProduct->status == ShopProductStatus::APPROVED->value) {
                return ApiResponse::failure('To disable this product please contact with Admin', Response::HTTP_FORBIDDEN);
            }

            $EProduct->update([
                'status' => $EProduct->status ==
                    ShopProductStatus::DISSABLED->value ?
                    ShopProductStatus::APPROVED->value :
                    ShopProductStatus::DISSABLED->value,
            ]);
            $newStatus = ShopProductStatus::label()[$EProduct->status];

            activity()
                ->useLog('product-status-update')
                ->event('updated')
                ->performedOn($EProduct)
                ->causedBy(auth()->user())
                ->withProperties([
                    'old' => $oldStatus,
                    'new' => $newStatus,
                ])
                ->log('Product status updated by ' . auth()->user()->name);

            return ApiResponse::success('E-Product Status Updated Successfully', $EProduct, Response::HTTP_OK);
        } catch (Exception $e) {
            return ApiResponse::failure('Something went wrong', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /*
     * Disables multiple e-products in bulk.
     */
    public function bulkStatus(ProductDisableRequest $request): JsonResponse
    {
        try {
            $request->validated();

            $productIds = $request->input('products');

            // Verify that all product IDs belong to the merchant
            $merchant                 = auth()->user()->merchant;
            $merchantId               = $merchant->id;
            if ($merchant?->shop_status == MerchantStatus::Suspended) {
                return ApiResponse::failure('Your shop is Inactive. You can\'t change product status.', Response::HTTP_FORBIDDEN);
            }
            $productsBelongToMerchant = ShopProduct::where('merchant_id', $merchantId)
                ->whereIn('id', $productIds)
                ->count();

            if ($productsBelongToMerchant !== count($productIds)) {
                return ApiResponse::failure('One or more product IDs do not belong to the merchant.', Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $status = match ($request->input('status')) {
                'enable'  => ShopProductStatus::APPROVED->value,
                'disable' => ShopProductStatus::DISSABLED->value,
                default   => ShopProductStatus::DISSABLED->value
            };

            if ($request->input('status') === 'enable') {
                $blockedCount = ShopProduct::where('merchant_id', $merchantId)
                    ->whereIn('id', $productIds)
                    ->whereIn('status', [
                        ShopProductStatus::PENDING->value,
                        ShopProductStatus::REJECTED->value,
                        ShopProductStatus::DISSABLED->value,
                    ])
                    ->count();

                if ($blockedCount > 0) {
                    return ApiResponse::failure('You can\'t enable selected Products', Response::HTTP_FORBIDDEN);
                }
            }

            if ($request->input('status') === 'disable') {
                $approvedCount = ShopProduct::where('merchant_id', $merchantId)
                    ->whereIn('id', $productIds)
                    ->where('status', ShopProductStatus::APPROVED->value)
                    ->count();

                if ($approvedCount > 0) {
                    return ApiResponse::failure('You can\'t disable selected Products', Response::HTTP_FORBIDDEN);
                }

                $pendingOrInactiveCount = ShopProduct::where('merchant_id', $merchantId)
                    ->whereIn('id', $productIds)
                    ->where(function ($q) {
                        $q->where('status', ShopProductStatus::PENDING->value);
                    })
                    ->count();

                if ($pendingOrInactiveCount > 0) {
                    return ApiResponse::failure('You can\'t disable pending Products', Response::HTTP_FORBIDDEN);
                }
            }

            ShopProduct::where('merchant_id', $merchantId)
                ->whereIn('id', $productIds)
                ->where('active_status', '!=', 0)
                ->where('status', '!=', ShopProductStatus::PENDING->value)
                ->update(['status' => $status]);

            return ApiResponse::success('Bulk disable E-Product Status Updated Successfully.', [], Response::HTTP_OK);
        } catch (Exception $e) {
            return ApiResponse::failure('Something went wrong', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
