<?php

namespace App\Imports;

use App\Models\Category\Category;
use App\Models\Category\SubCategory;
use App\Models\Category\SubCategoryChild;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CategoriesImport implements ToModel, WithHeadingRow
{
    private $currentCategory = null;

    private $currentSubCategory = null;

    public function model(array $row)
    {
        try {
            // Process category if present
            if (! empty($row['categories'])) {
                $categoryName = trim($row['categories']);
                $categorySlug = Str::slug($categoryName);
                // Try to find existing category or create new one
                $this->currentCategory = Category::where('slug', $categorySlug)->first();
                if (! $this->currentCategory) {
                    $this->currentCategory = Category::create([
                        'name'     => $categoryName,
                        'slug'     => $categorySlug,
                        'status'   => 1,
                        'added_by' => 1,
                    ]);
                }
            }
            // Only process subcategory if we have a valid category (either from current or previous row)
            if (! empty($row['sub_categories']) && $this->currentCategory) {
                $subCategoryName = trim($row['sub_categories']);
                $subCategorySlug = Str::slug($this->currentCategory->name.'-'.$subCategoryName);
                // Check for existing subcategory using both name and category_id
                $this->currentSubCategory = SubCategory::where('name', $subCategoryName)
                    ->where('category_id', $this->currentCategory->id)
                    ->first();
                if (! $this->currentSubCategory) {
                    $this->currentSubCategory = SubCategory::create([
                        'name'        => $subCategoryName,
                        'slug'        => $subCategorySlug,
                        'category_id' => $this->currentCategory->id,
                        'status'      => 1,
                    ]);
                }
            }
            // Only process sub-subcategories if we have a valid subcategory
            if (! empty($row['super_subcategory']) && $this->currentSubCategory) {
                $subSubCategories = array_filter(
                    explode("\n", $row['super_subcategory']),
                    fn ($value) => trim($value) !== ''
                );
                foreach ($subSubCategories as $subSubCategoryName) {
                    $subSubCategoryName = trim($subSubCategoryName);
                    $subSubCategorySlug = Str::slug($this->currentSubCategory->name.'-'.$subSubCategoryName.'-'.$this->currentSubCategory->id);

                    // Check for existing sub-subcategory using both name, sub_category_id and slug
                    $existingChild = SubCategoryChild::where('name', $subSubCategoryName)
                        ->where('sub_category_id', $this->currentSubCategory->id)
                        ->first();

                    if (! $existingChild) {
                        try {
                            SubCategoryChild::create([
                                'name'            => $subSubCategoryName,
                                'slug'            => $subSubCategorySlug,
                                'sub_category_id' => $this->currentSubCategory->id,
                                'status'          => 1,
                                'added_by'        => 1,
                            ]);
                        } catch (\Illuminate\Database\QueryException $e) {
                            if ($e->getCode() == 23000) {
                                Log::info('Duplicate entry skipped: '.$subSubCategoryName);
                            } else {
                                throw $e;
                            }
                        }
                    }

                }
            }

            return null;
        } catch (\Exception $e) {
            // Only log actual errors, not expected conditions
            if (! $this->currentCategory && ! empty($row['sub_categories'])) {
                Log::info('Skipping row - no category context available for subcategory: '.$row['sub_categories']);
            } else {
                Log::error('Error processing row: '.json_encode($row).' - '.$e->getMessage().' - '.$e->getTraceAsString());
            }

            return null;
        }
    }
}
