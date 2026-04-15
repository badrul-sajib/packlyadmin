<?php

namespace App\Services;

use App\Exceptions\CategoryCreationException;
use App\Models\Category\Category;
use App\Models\Category\SubCategory;
use App\Models\Category\SubCategoryChild;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class CategoryService
{
    const MAIN = '1';

    const SUB = '2';

    const CHILD = '3';

    public static function getCategories(): JsonResponse
    {
        try {
           
            $categories = self::categoryQuery();
            return success('Categories retrieved successfully', $categories);
        } catch (Exception $e) {
            Log::error('getCategories error: '.$e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return failure('Something went wrong', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    public static function categoryQuery()
    {
        try {
            return Cache::rememberForever('frontend-categories', function () {

                return Category::active()
                    ->where(function ($q) {

                        $q->where('has_products', 1)
                            ->orWhereHas('subcategories', function ($sq) {
                                $sq->where(function ($ssq) {

                                    $ssq->where('has_products', 1)
                                        ->orWhereHas('subchilds', fn ($sssq) => $sssq->where('has_products', 1));
                                });
                            });
                    })
                    ->with([
                        'media',

                        'subcategories' => function ($q) {
                            $q->where(function ($sq) {
                                $sq->where('has_products', 1)
                                    ->orWhereHas('subchilds', fn ($ssq) => $ssq->where('has_products', 1));
                            })
                                ->with([
                                    'media',

                                    'subchilds' => function ($sq) {
                                        $sq->where('has_products', 1)->with('media');
                                    },
                                ])
                                ->orderBy('name');
                        },
                    ])
                    ->orderBy('name')
                    ->get()
                    ->map(function ($category) {
                        return [
                            'id'            => $category->id,
                            'name'          => $category->name,
                            'slug'          => $category->slug,
                            'image'         => $category->image,

                            'subcategories' => $category->subcategories->map(function ($subcategory) {
                                return [
                                    'id'        => $subcategory->id,
                                    'name'      => $subcategory->name,
                                    'slug'      => $subcategory->slug,
                                    'image'     => $subcategory->image,

                                    'subchilds' => $subcategory->subchilds->map(function ($subchild) {
                                        return [
                                            'id'    => $subchild->id,
                                            'name'  => $subchild->name,
                                            'slug'  => $subchild->slug,
                                            'image' => $subchild->image,
                                        ];
                                    })->values(),
                                ];
                            })->values(),
                        ];
                    })->values();
            });
        } catch (\Throwable $th) {
            return [];
        }
    }


    public function getAllCategories($request)
    {
        return match ($request->type ?? '1') {
            self::MAIN  => $this->getMainCategory($request),
            self::SUB   => $this->getSubCategory($request),
            self::CHILD => $this->getChildCategory($request),
            default     => [],
        };
    }

    private function getBaseQuery($model, $request, $type)
    {
        $perPage = $request->perPage ?? 10;
        $page    = $request->page    ?? 1;

        return $model::query()
            ->when($type === self::MAIN, fn ($query) => $query->with('media'))
            ->when($type === self::SUB, fn ($query) => $query->with(['category', 'media']))
            ->when($type === self::CHILD, fn ($query) => $query->with(['subCategory', 'subCategory.category', 'media']))
            ->when(
                $request->search,
                fn ($query, $search) => $query->where('name', 'like', "%{$search}%")->orWhere('commission', 'like', "%{$search}%")
            )
            ->when(
                $request->category_id,
                fn ($query, $categoryId) => $query->where('category_id', $categoryId)
            )
            ->withCount('products')
            ->orderBy('id', 'asc')
            ->paginate($perPage, ['*'], 'page', $page)
            ->withQueryString();
    }

    public function getMainCategory($request)
    {
        return $this->getBaseQuery(Category::class, $request, self::MAIN);
    }

    public function getSubCategory($request)
    {
        return $this->getBaseQuery(SubCategory::class, $request, self::SUB);
    }

    public function getChildCategory($request)
    {
        return $this->getBaseQuery(SubCategoryChild::class, $request, self::CHILD);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws CategoryCreationException
     */
    public function findCategory($id): Category|SubCategory|SubCategoryChild
    {
        $type = request()->get('type') ?? '0';

        return match ($type) {
            self::MAIN  => Category::find($id),
            self::SUB   => SubCategory::find($id),
            self::CHILD => SubCategoryChild::find($id),
            default     => throw new CategoryCreationException('Invalid category type')
        };
    }

    /**
     * @throws Throwable
     */
    public function storeCategory(array $data): Category|SubCategory|SubCategoryChild
    {
        return DB::transaction(function () use ($data) {
            $data['added_by'] = auth()->id();

            return match ($data['type']) {
                self::MAIN  => $this->createCategoryBase($data, Category::class),
                self::SUB   => $this->createCategoryBase($data, SubCategory::class),
                self::CHILD => $this->createCategoryBase($data, SubCategoryChild::class),
                default     => throw new CategoryCreationException('Invalid category type')
            };
        });
    }

    /**
     * @throws Throwable
     */
    public function updateCategory($data, $id)
    {
        return DB::transaction(function () use ($data, $id) {
            $category = $this->findCategory($id);
            $this->updateCategoryBase($data, $category);
        });
    }

    private function updateCategoryBase(array $data, $category): void
    {
        $categoryData = [
            'name'            => $data['name'],
            'status'          => $data['status'],
            'category_id'     => $data['category_id']         ?? null,
            'sub_category_id' => $data['sub_category_id']     ?? null,
        ];

        if (isset($data['image'])) {
            $categoryData['image'] = $data['image'];
        }

        $category->update($categoryData);
    }

    private function createCategoryBase(array $data, $modelClass)
    {
        $categoryData = [
            'name'            => $data['name'],
            'status'          => $data['status'],
            'category_id'     => $data['category_id']         ?? null,
            'sub_category_id' => $data['sub_category_id']     ?? null,
        ];

        $category = $modelClass::create($categoryData);

        if (isset($data['image'])) {
            $category->image = $data['image'];
            $category->save();
        }

        return $category;
    }

    /**
     * @throws Throwable
     */
    public function updateCommission(array $data)
    {
        return DB::transaction(function () use ($data) {
            $id = $data['id'];
            $category = $this->findCategory($id);
            $category->commission = $data['commission'];
            $category->commission_type = $data['commission_type'] ?? 'percent';
            $category->save();
        });
    }

    public function getCommissionLogs($id)
    {
        $category = $this->findCategory($id);
        return $category->activities()->latest()->get()->map(function ($activity) {
            return [
                'causer' => $activity->causer ? $activity->causer->name : 'System',
                'date' => $activity->created_at->format('Y-m-d H:i:s'),
                'properties' => $activity->properties
            ];
        });
    }

    /**
     * @throws Throwable
     */
    public function deleteCategory($id)
    {
        return DB::transaction(function () use ($id) {
            $category = $this->findCategory($id);

            if ($category->products()->exists()) {
                throw new Exception('Category has products, cannot delete');
            }
            if ($category->coupons && $category->coupons()->exists()) {
                throw new Exception('Category has coupons, cannot delete');
            }
            if ($category->subcategories && $category->subcategories()->exists()) {
                throw new Exception('Category has subcategories, cannot delete');
            }
            if ($category->subchilds && $category->subchilds()->exists()) {
                throw new Exception('Category has subchilds, cannot delete');
            }
            $category->delete();
        });
    }
}
