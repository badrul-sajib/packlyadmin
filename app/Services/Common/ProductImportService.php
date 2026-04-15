<?php

namespace App\Services\Common;

use App\Jobs\CreateStockOrders;
use App\Models\Brand\Brand;
use App\Models\Category\Category;
use App\Models\Category\SubCategory;
use App\Models\Category\SubCategoryChild;
use App\Models\Product\Product;
use App\Models\Product\ProductDetails;
use App\Models\Unit\Unit;
use App\Models\User\User;
use App\Services\ApiResponse;
use Exception;
use Illuminate\Bus\Batch;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ProductImportService
{
    /**
     * Validate the JSON input data.
     */
    public function validateInput(array $data, array $rowRules, array $bulkRules): array
    {
        $errors = [];

        if (! is_array($data['products']) || empty($data['products'])) {
            throw new Exception('Products array is missing or empty.', Response::HTTP_NOT_FOUND);
        }

        // Validate the first row
        $validator = Validator::make($data['products'][0], $rowRules);
        if ($validator->fails()) {
            throw new Exception('Invalid CSV format.' . $validator->errors()->first(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        if (count($data['products']) > 500) {
            throw new Exception('Maximum 500 products allowed per import.', Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // Validate all products
        $validator = Validator::make($data, $bulkRules);
        if ($validator->fails()) {
            foreach ($validator->errors()->messages() as $key => $messages) {
                if (str_starts_with($key, 'products.')) {
                    $parts = explode('.', $key);
                    $index = $parts[1];
                    $field = $parts[2] ?? 'unknown';

                    $errors[$index] = [
                        'row' => $index + 1,
                        'name' => $data['products'][$index]['name'] ?? 'Unknown Product',
                        'errors' => [$field => $messages],
                    ];
                }
            }

            return $errors;
        }

        return $errors;
    }

    /**
     * Apply corrections to product data if provided.
     */
    public function applyCorrections(array &$data): void
    {
        if (isset($data['corrections'])) {
            foreach ($data['corrections'] as $correction) {
                if (isset($correction['row'])) {
                    $index = $correction['row'] - 1;
                    if (isset($data['products'][$index])) {
                        $data['products'][$index] = array_merge(
                            $data['products'][$index],
                            $correction['fields']
                        );
                    }
                }
            }
        }
    }

    /**
     * Validate individual product data for existence and pricing rules.
     */
    public function validateProductData(array $products, int $merchantId): array
    {
        $errors = [];

        foreach ($products as $index => $productData) {
            $rowErrors = [];
            $sku = $productData['sku'] ?? $this->generateSKU($merchantId);

            $isValidName = preg_match('/^[\x{0980}-\x{09FF}a-zA-Z0-9\s\-,\.\/\%\(\)\{\}]+$/u', $productData['name']);

            $slug = Str::slug($productData['name']);

            if (! $isValidName || ! $slug) {
                $rowErrors['name'] = [
                    'message' => 'Enter a valid product name',
                    'value' => $productData['name'],
                ];
            }

            // Check for duplicate products
            $existingProduct = Product::where('merchant_id', $merchantId)
                ->where(function ($query) use ($productData, $sku) {
                    $query->where('name', $productData['name'])->orWhere('sku', $sku);
                })
                ->first();

            if ($existingProduct) {
                $rowErrors['name'] = [
                    'message' => "Product with name '{$productData['name']}' or SKU '{$sku}' already exists",
                    'value' => $productData['name'],
                ];
            }

            // Check category
            $category = Category::where('name', $productData['category_name'])->first();
            if (! $category) {
                $rowErrors['category_name'] = [
                    'message' => 'Category not found',
                    'value' => $productData['category_name'],
                ];
                if (! empty($productData['sub_category_name'])) {
                    $rowErrors['sub_category_name'] = [
                        'message' => 'Category not found',
                        'value' => $productData['sub_category_name'],
                    ];
                }
                if (! empty($productData['sub_category_child_name'])) {
                    $rowErrors['sub_category_child_name'] = [
                        'message' => 'Category not found',
                        'value' => $productData['sub_category_child_name'],
                    ];
                }
            } else {
                // Check sub-category exists if provided
                if (! empty($productData['sub_category_name'])) {
                    $subCategory = SubCategory::where('name', $productData['sub_category_name'])
                        ->where('category_id', $category->id)
                        ->first();

                    if (! $subCategory) {
                        $rowErrors['sub_category_name'] = [
                            'message' => 'Sub category not found in the selected category',
                            'value' => $productData['sub_category_name'],
                        ];

                        if (! empty($productData['sub_category_child_name'])) {
                            $rowErrors['sub_category_child_name'] = [
                                'message' => 'Sub-category not found',
                                'value' => $productData['sub_category_child_name'],
                            ];
                        }
                    } else {
                        // Check sub-category child exists if provided
                        if (! empty($productData['sub_category_child_name'])) {
                            $subCategoryChild = SubCategoryChild::where('name', $productData['sub_category_child_name'])
                                ->where('sub_category_id', $subCategory->id)
                                ->first();

                            if (! $subCategoryChild) {
                                $rowErrors['sub_category_child_name'] = [
                                    'message' => 'Child category not found in the selected sub category',
                                    'value' => $productData['sub_category_child_name'],
                                ];
                            }
                        }
                    }
                } elseif (! empty($productData['sub_category_child_name'])) {
                    $rowErrors['sub_category_child_name'] = [
                        'message' => 'Sub-category is required for child category',
                        'value' => $productData['sub_category_child_name'],
                    ];
                }
            }

            // Check brand if provided
            if (! empty($productData['brand_name'])) {
                $brand = Brand::where('name', $productData['brand_name'])->first();
                if (! $brand) {
                    $rowErrors['brand_name'] = [
                        'message' => 'Brand not found',
                        'value' => $productData['brand_name'],
                    ];
                }
            }

            // Check unit if provided
            if (! empty($productData['unit_name'])) {
                $unit = Unit::where('name', $productData['unit_name'])->first();
                if (! $unit) {
                    $rowErrors['unit_name'] = [
                        'message' => 'Unit not found',
                        'value' => $productData['unit_name'],
                    ];
                }
            }

            // Check pricing rules
            if (isset($productData['purchase_price']) && isset($productData['regular_price']) && $productData['purchase_price'] > $productData['regular_price']) {
                $rowErrors['purchase_price'] = [
                    'message' => "Purchase price {$productData['purchase_price']} cannot be greater than regular price {$productData['regular_price']}",
                    'value' => $productData['purchase_price'],
                ];
                $rowErrors['regular_price'] = [
                    'message' => "Regular price {$productData['regular_price']} cannot be less than purchase price {$productData['purchase_price']}",
                    'value' => $productData['regular_price'],
                ];
            }

            if (isset($productData['regular_price']) && isset($productData['discount_price']) && $productData['regular_price'] < $productData['discount_price']) {
                $rowErrors['regular_price'] = [
                    'message' => "Regular price {$productData['regular_price']} cannot be less than discount price {$productData['discount_price']}",
                    'value' => $productData['regular_price'],
                ];
                $rowErrors['discount_price'] = [
                    'message' => "Discount price {$productData['discount_price']} cannot be greater than regular price {$productData['regular_price']}",
                    'value' => $productData['discount_price'],
                ];
            }

            if (! empty($rowErrors)) {
                $errors[$index] = [
                    'row' => $index + 1,
                    'name' => $productData['name'] ?? 'Unknown Product',
                    'errors' => $rowErrors,
                ];
            }
        }

        return $errors;
    }

    /**
     * Import products into the database.
     */
    public function importProducts(array $products, int $merchantId, ?string $paymentDate = null): array
    {
        $successfulImports = [];
        $failedImports = [];
        $stockOrderJobs = [];

        foreach ($products as $productData) {
            DB::beginTransaction();

            try {
                $sku = $productData['sku'] ?? $this->generateSKU($merchantId);

                // Verify product doesn't exist
                $existingProduct = Product::where('merchant_id', $merchantId)
                    ->where(function ($query) use ($productData, $sku) {
                        $query->where('name', $productData['name'])->orWhere('sku', $sku);
                    })
                    ->first();

                if ($existingProduct) {
                    throw new Exception("Product with name '{$productData['name']}' or SKU '{$sku}' already exists");
                }

                // Fetch related entities
                $category = Category::where('name', $productData['category_name'])->first();
                if (! $category) {
                    throw new Exception("Category '{$productData['category_name']}' not found");
                }

                $subCategory = null;
                if (! empty($productData['sub_category_name'])) {
                    $subCategory = SubCategory::where('name', $productData['sub_category_name'])
                        ->where('category_id', $category->id)
                        ->first();
                    if (! $subCategory) {
                        throw new Exception("Sub-category '{$productData['sub_category_name']}' not found in category '{$productData['category_name']}'");
                    }
                }

                $subCategoryChild = null;
                if (! empty($productData['sub_category_child_name'])) {
                    if (! $subCategory) {
                        throw new Exception("Sub-category is required for child category '{$productData['sub_category_child_name']}'");
                    }
                    $subCategoryChild = SubCategoryChild::where('name', $productData['sub_category_child_name'])
                        ->where('sub_category_id', $subCategory->id)
                        ->first();
                    if (! $subCategoryChild) {
                        throw new Exception("Child category '{$productData['sub_category_child_name']}' not found in sub-category '{$productData['sub_category_name']}'");
                    }
                }

                $brand = null;
                if (! empty($productData['brand_name'])) {
                    $brand = Brand::where('name', $productData['brand_name'])->first();
                    if (! $brand) {
                        throw new Exception("Brand '{$productData['brand_name']}' not found");
                    }
                }

                $unit = null;
                if (! empty($productData['unit_name'])) {
                    $unit = Unit::where('name', $productData['unit_name'])->first();
                    if (! $unit) {
                        throw new Exception("Unit '{$productData['unit_name']}' not found");
                    }
                }

                $slug = $this->validateNameAndGenerateSlug($productData['name'] ?? '');

                // Create product
                $product = Product::create([
                    'merchant_id' => $merchantId,
                    'name' => $productData['name'],
                    'slug' => $slug,
                    'description' => $productData['description'],
                    'specification' => $productData['specification'],
                    'sku' => $sku,
                    'category_id' => $category->id,
                    'sub_category_id' => $subCategory ? $subCategory->id : null,
                    'sub_category_child_id' => $subCategoryChild ? $subCategoryChild->id : null,
                    'brand_id' => $brand ? $brand->id : null,
                    'unit_id' => $unit ? $unit->id : null,
                    'product_type_id' => $productData['product_type'] === 'single' ? 1 : 2,
                    'weight' => $productData['weight'],
                ]);

                // Determine selling type
                $selling_type = 1; // default
                if (! empty($productData['selling_type'])) {
                    $normalized = strtolower($productData['selling_type']);
                    $normalized = str_replace(['&', ' ', '|'], ',', $normalized);
                    $types = array_filter(explode(',', $normalized));

                    if (in_array('retail', $types) && in_array('wholesale', $types)) {
                        $selling_type = 3;
                    } elseif (in_array('retail', $types)) {
                        $selling_type = 1;
                    } elseif (in_array('wholesale', $types)) {
                        $selling_type = 2;
                    }
                }

                // Create product details
                $productDetailData = [
                    'product_id' => $product->id,
                    'selling_type_id' => $selling_type,
                ];

                if ($product->product_type_id == Product::$PRODUCT_TYPE_SINGLE) {
                    $productDetailData = array_merge($productDetailData, [
                        'purchase_price' => $productData['purchase_price'] ?? 0,
                        'e_price' => $productData['e_price'] ?? 0,
                        'e_discount_price' => $productData['e_discount_price'] ?? 0,
                        'regular_price' => $productData['regular_price'] ?? 0,
                        'discount_price' => $productData['discount_price'] ?? 0,
                        'wholesale_price' => $productData['wholesale_price'] ?? 0,
                        'minimum_qty' => $productData['minimum_qty'] ?? 0,
                    ]);
                } else {
                    $productDetailData = array_merge($productDetailData, [
                        'purchase_price' => 0,
                        'e_price' => 0,
                        'e_discount_price' => 0,
                        'regular_price' => 0,
                        'discount_price' => 0,
                        'wholesale_price' => 0,
                        'minimum_qty' => 0,
                    ]);
                }

                ProductDetails::create($productDetailData);

                // Handle opening stock in Background Queue
                if (! empty($productData['opening_stock']) && $product->product_type_id == Product::$PRODUCT_TYPE_SINGLE) {
                    // Collect prices for the job
                    $prices = [
                        'purchase_price' => $productDetailData['purchase_price'],
                        'e_price' => $productDetailData['e_price'],
                        'e_discount_price' => $productDetailData['e_discount_price'],
                        'regular_price' => $productDetailData['regular_price'],
                        'discount_price' => $productDetailData['discount_price'],
                        'wholesale_price' => $productDetailData['wholesale_price'],
                    ];

                    $stockOrderJobs[] = new CreateStockOrders(
                        $product->id,
                        $merchantId,
                        $productData['opening_stock'],
                        $prices,
                        $paymentDate
                    );
                }

                $successfulImports[] = [
                    'name' => $product->name,
                    'sku' => $product->sku,
                    'product_id' => $product->id,
                ];

                DB::commit();
            } catch (Exception $e) {
                DB::rollBack();
                $failedImports[] = [
                    'name' => $productData['name'] ?? 'Unknown Product',
                    'error' => $e->getMessage(),
                ];
            }
        }

        // Dispatch Batch of Stock Order Jobs if any
        if (! empty($stockOrderJobs)) {
            try {
                $userId = Auth::id(); // Get current user ID

                Bus::batch($stockOrderJobs)
                    ->then(function (Batch $batch) use ($userId) {
                        $user = User::find($userId);
                        if ($user) {
                            $message = 'Product import completed successfully.';
                            $user->merchant?->sendNotification(
                                $message,
                                'Product imported, now you can check your inventory for details.',
                                '/product'
                            );
                        }
                    })
                    ->catch(function (Batch $batch, Throwable $e) use ($userId) {
                        $user = User::find($userId);
                        if ($user) {
                            $message = 'Product import encountered errors.';
                            $user->merchant?->sendNotification(
                                $message,
                                'There were some issues during product import. Please review your products.',
                                '/product'
                            );
                        }
                    })
                    ->name('Import Product Stock')
                    ->dispatch();
            } catch (Throwable $e) {
                Log::error('Product Import Batch Dispatch Failed: ' . $e->getMessage());
            }
        }

        return [
            'success' => [
                'count' => count($successfulImports),
                'products' => $successfulImports,
            ],
            'failures' => [
                'count' => count($failedImports),
                'products' => $failedImports,
            ],
        ];
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

    /**
     * Process the product import request.
     */
    public function processImport(array $data, array $rowRules, array $bulkRules, int $merchantId, ?string $paymentDate = null): JsonResponse
    {
        try {
            // Validate input JSON
            $errors = $this->validateInput($data, $rowRules, $bulkRules);
            if ($errors) {
                return ApiResponse::success('Validation completed', [
                    'has_errors' => true,
                    'errors' => array_values($errors),
                    'total_rows' => count($data['products']),
                    'error_rows' => count($errors),
                    'valid_rows' => count($data['products']) - count($errors),
                ]);
            }

            // Apply corrections
            $this->applyCorrections($data);

            // Validate product data
            $productErrors = $this->validateProductData($data['products'], $merchantId);
            if ($productErrors) {
                return ApiResponse::success('Validation completed', [
                    'has_errors' => true,
                    'errors' => array_values($productErrors),
                    'total_rows' => count($data['products']),
                    'error_rows' => count($productErrors),
                    'valid_rows' => count($data['products']) - count($productErrors),
                ]);
            }

            // Import products
            $response = $this->importProducts($data['products'], $merchantId, $paymentDate);

            return ApiResponse::success('Import request successfully. Inventory updates may take some time.', $response, Response::HTTP_OK);
        } catch (Exception $e) {
            return ApiResponse::failure('Import failed', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function validateNameAndGenerateSlug(string $name): string|Exception
    {
        if (! preg_match('/^[\p{Bengali}A-Za-z0-9০-৯\s\-\_&\/\%\(\)\{\}]+$/u', $name)) {
            throw new Exception('The name contains invalid characters.');
        }

        // Reject if only symbols or spaces
        if (preg_match('/^[\-\_\.\/\+\*\#\@\!\s]+$/u', $name)) {
            throw new Exception('The name cannot consist only of symbols or spaces.');
        }

        // Reject if only English numbers
        if (preg_match('/^[0-9]+$/u', $name)) {
            throw new Exception('The name cannot consist only of numbers.');
        }

        // Reject if only Bangla numbers (০-৯)
        if (preg_match('/^[০-৯]+$/u', $name)) {
            throw new Exception('The name cannot consist only of Bangla numbers.');
        }

        // Create unique slug
        $baseSlug = Str::slug($name);
        $slug = $baseSlug;

        $counter = 1;
        while (Product::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        if (blank($slug)) {
            throw new Exception('Could not generate unique slug. Please change the product name.');
        }

        return $slug;
    }

    /**
     * Validate products without importing (for preview in frontend)
     */
    public function validateOnlyImport(array $data, array $rowRules, array $bulkRules, int $merchantId): JsonResponse
    {
        try {
            // Step 1: Basic input validation (same as processImport)
            $initialErrors = $this->validateInput($data, $rowRules, $bulkRules);

            $allRowDetails = [];
            $hasAnyError = filled($initialErrors);

            foreach ($data['products'] as $index => $productData) {
                $rowErrors = null;

                // Collect initial validation errors for this row (from bulk validator)
                if (isset($initialErrors[$index])) {
                    foreach ($initialErrors[$index]['errors'] as $field => $messages) {
                        $rowErrors[$field] = $messages;
                    }
                }

                // Step 2: Apply business logic validation (same as processImport)
                $businessErrors = $this->validateProductData([$productData], $merchantId);

                if (filled($businessErrors)) {
                    $errors = Arr::get($businessErrors, '0.errors', null);
                    foreach ($errors as $field => $errorInfo) {
                        $rowErrors[$field] = [$errorInfo['message']]; // standardize as array of strings
                    }
                    $hasAnyError = true;
                }

                // Optional: Validate slug generation (will throw if invalid name)
                try {
                    $this->validateNameAndGenerateSlug($productData['name'] ?? '');
                } catch (Exception $e) {
                    $rowErrors['name'] = $rowErrors['name'] ?? null;
                    $rowErrors['name'][] = $e->getMessage();
                    $hasAnyError = true;
                }

                $allRowDetails[] = [
                    'row_index' => $index + 1, // 1-based for Excel
                    'data' => $productData,
                    'errors' => $rowErrors,
                ];
            }

            return ApiResponse::success('Product import validation completed', [
                'is_valid' => ! $hasAnyError,
                'total_rows' => count($data['products']),
                'valid_rows' => count($data['products']) - count(array_filter($allRowDetails, fn($r) => filled($r['errors']))),
                'rows' => $allRowDetails,
            ], Response::HTTP_OK);
        } catch (Exception $e) {
            return ApiResponse::failure($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
