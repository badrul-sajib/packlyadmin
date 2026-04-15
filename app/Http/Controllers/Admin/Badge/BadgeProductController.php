<?php

namespace App\Http\Controllers\Admin\Badge;

use App\Models\Product\Badge;
use App\Models\Product\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\Product\BadgeProductVariation;
use App\Http\Requests\Admin\BadgeProductRequest;

class BadgeProductController extends Controller
{
    public function store(BadgeProductRequest $request)
    {
        $data = $request->validated();

        DB::beginTransaction();

        try {
            $badge = Badge::with('badge_products.badgeProductVariations')->where('id', $data['badge_id'])->first();

            $productIds = $data['product_ids'] ?? [];
            $variantPayload = $data['varient'] ?? [];
            $badgeProducts  = $badge->badgeProducts;
            // 1 — Sync badge_products
            // FULL DETACH / ATTACH

            $syncResult = $badge->badge_products()->sync($productIds);

            // Because of FK cascade:
            // Detaching products automatically removes their badge_product_variations
            

            // 2 — Assign variations
            foreach ($badgeProducts as $bp) {

                $pid = $bp->product_id;
                $newVariants = $variantPayload[$pid] ?? [];
            
                BadgeProductVariation::where('badge_product_id', $bp->id)->delete();

                // Insert new
                foreach ($newVariants as $vid) {
                    BadgeProductVariation::create([
                        'badge_product_id' => $bp->id,
                        'product_variation_id' => $vid,
                    ]);
                }
            }

            // 3 — Update product.badge_label
            foreach ($badge->badgeProducts as $bp) {

                $product = Product::find($bp->product_id);

                $productVariations = count($product->variations ?? []);
                $badgeProductVariations = count($bp->badgeProductVariations ?? []);

                if ($productVariations == 0 || $badgeProductVariations == 0) {
                    $product->update(['badge_label' => $badge->type_label]);
                }elseif($productVariations == $badgeProductVariations){
                    $product->update(['badge_label' => null]);
                }else{
                    $product->update(['badge_label' => $badge->type_label]);
                }
            }

            // Detached products → remove label
            if (!empty($syncResult['detached'])) {
                Product::whereIn('id', $syncResult['detached'])
                    ->update(['badge_label' => null]);
            }

            DB::commit();

            return success('Products added to badge successfully');

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return failure('Something went wrong', 500);
        }
    }

}
