<?php

namespace App\Http\Controllers\Admin\ActivityLog;

use App\Http\Controllers\Controller;
use App\Models\Category\Category;
use App\Models\Coupon\Coupon;
use App\Models\Merchant\Merchant;
use App\Models\Order\Order;
use App\Models\PrimeView\PrimeView;
use App\Models\PrimeView\PrimeViewProduct;
use App\Models\Product\Badge;
use App\Models\Product\Product;
use App\Models\Shop\ShopProduct;
use App\Models\User\User;
use Google\Service\CloudSearch\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ActivityLogController extends Controller
{
    /**
     * Mapping of entity names to their corresponding model classes
     */
    private array $entityModelMap = [
        'merchant'           => Merchant::class,
        'coupon'             => Coupon::class,
        'badge'              => Badge::class,
        'category'           => Category::class,
        'user'               => User::class,
        'order'              => Order::class,
        'prime_view'         => PrimeView::class,
        'prime_view_product' => PrimeViewProduct::class,
        'product'            => Product::class,
        'shop_product'       => ShopProduct::class,
        'giveaway'           => \App\Models\Giveaway\Giveaway::class,
        // Add more entities as needed
    ];

    public function getActivityLog(Request $request, $entity, $id)
    {
        try {
            // Validate entity type
            if (! array_key_exists($entity, $this->entityModelMap)) {
                return response()->json([
                    'error'   => 'Invalid entity type',
                    'message' => 'The specified entity type is not supported.',
                ], 400);
            }

            // Get the model class
            $modelClass = $this->entityModelMap[$entity];

            // Find the entity
            $model = $modelClass::find($id);

            if (! $model) {
                return response()->json([
                    'error'   => 'Entity not found',
                    'message' => 'The specified '.$entity.' was not found.',
                ], 404);
            }

            // Build the activity query
            $query = $model->activities()->with('causer');

            // Apply log name filter if provided
            if ($request->has('log_name') && $request->get('log_name')) {
                $query->where('log_name', $request->get('log_name'));
            }

            // Get activities ordered by creation date (newest first)
            $activities = $query->orderBy('created_at', 'desc')->get();

            // Prepare data for the component
            $componentData = [
                'activities'   => $activities,
                'title'        => $this->generateTitle($entity, $request->get('log_name')),
                'emptyMessage' => $this->generateEmptyMessage($entity),
            ];

            // Return the rendered component
            return view('components.activity-log', $componentData)->render();

        } catch (\Exception $e) {
            // Log the error
            Log::error('Activity log fetch error: '.$e->getMessage(), [
                'entity' => $entity,
                'id'     => $id,
                'trace'  => $e->getTraceAsString(),
            ]);

            return response()->json([
                'error'   => 'Server error',
                'message' => 'An error occurred while loading the activity log.',
            ], 500);
        }
    }

    /**
     * Generate appropriate title based on entity and log name
     */
    private function generateTitle(string $entity, ?string $logName = null): string
    {
        $entityName = ucfirst($entity);

        if ($logName) {
            // Convert log name to readable format
            $logNameFormatted = ucwords(str_replace(['-', '_'], ' ', $logName));

            return "{$entityName} - {$logNameFormatted}";
        }

        return "{$entityName} Activity Log";
    }

    /**
     * Generate appropriate empty message based on entity
     *
     * @param  string  $entity
     * @return string
     */
    private function generateEmptyMessage($entity)
    {
        $entityName = strtolower($entity);

        return "No {$entityName} activities found";
    }

    /**
     * Add a new entity model mapping (useful for extending the controller)
     *
     * @return void
     */
    public function addEntityMapping(string $entity, string $modelClass)
    {
        $this->entityModelMap[$entity] = $modelClass;
    }

    /**
     * Get available entity types
     *
     * @return array
     */
    public function getAvailableEntities()
    {
        return array_keys($this->entityModelMap);
    }
}
