<?php

namespace Modules\Api\V1\Ecommerce\Cart\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Api\V1\Ecommerce\Cart\Http\Requests\CartDestroyItemRequest;
use Modules\Api\V1\Ecommerce\Cart\Http\Requests\CartStoreRequest;
use Modules\Api\V1\Ecommerce\Cart\Http\Resources\CartItemResource;
use App\Services\CartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class CartController extends Controller
{
    protected CartService $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function index(): JsonResponse
    {
        $cart = $this->cartService->getUserCart();

        return success('Cart retrieved successfully', CartItemResource::collection($cart->items));
    }

    public function store(CartStoreRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();

            $failedItems = $this->cartService->bulkAddOrUpdateItems($validated['items']);
            $cart        = $this->cartService->getUserCart();
            $cartItems   = $cart->items()->orderBy('created_at', 'desc')->get();

            // case 1: all failed
            if (count($failedItems) === count($validated['items'])) {
                $formatted = [];
                foreach ($failedItems as $index => $error) {
                    $key               = "items.$index";
                    $formatted[$key][] = $error['message'];
                }

                return validationError('All cart items failed to update', $formatted);
            }

            // case 2: some failed, some succeeded
            if (! empty($failedItems)) {
                return success('Cart partially updated. Some items could not be added.', [
                    'failed_items' => $failedItems,
                    'cart'         => CartItemResource::collection($cartItems),
                ]);
            }

            // case 3: all succeeded
            return success('Cart updated successfully', [
                'failed_items' => [],
                'cart'         => CartItemResource::collection($cartItems),
            ]);
        } catch (ValidationException $e) {
            return validationError('Validation failed', $e->errors());
        } catch (\Exception $e) {
            return failure('Failed to update cart', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroyItems(CartDestroyItemRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();

            $this->cartService->removeItems($validated['items']);

            return success('Selected items removed from cart');
        } catch (ValidationException $e) {
            return validationError('Validation failed', $e->errors());
        } catch (\Exception $e) {
            return failure('Failed to update cart', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
