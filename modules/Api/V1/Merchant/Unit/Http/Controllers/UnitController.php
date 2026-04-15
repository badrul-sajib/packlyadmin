<?php

namespace Modules\Api\V1\Merchant\Unit\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Api\V1\Merchant\Unit\Http\Requests\UnitRequest;
use App\Models\Unit\Unit;
use App\Services\ApiResponse;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class UnitController extends Controller
{
    public function __construct()
    {
        $this->middleware('shop.permission:show-units')->only('index', 'show');
        $this->middleware('shop.permission:create-unit')->only('store');
        $this->middleware('shop.permission:update-unit')->only('update');
        $this->middleware('shop.permission:delete-unit')->only('destroy');
        $this->middleware('shop.permission:change-unit-status')->only('status');
    }
    /*
     * Lists all units.
     */
    public function index(): JsonResponse
    {
        $units = Unit::where('merchant_id', auth()->user()->merchant->id)
            ->orderByDesc('id')
            ->select('id', 'name', 'slug', 'status')
            ->get();

        return ApiResponse::success('All Units retrieved successfully', $units, Response::HTTP_OK);
    }

    /*
     * Creates a new unit
     */
    public function store(UnitRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();

            $merchant = auth()->user()->merchant;
            if (! $merchant) {
                return ApiResponse::failure('You are not authorized to access this resource.', Response::HTTP_FORBIDDEN);
            }

            $unit = Unit::create([
                'name'        => $data['name'],
                'merchant_id' => $merchant->id,
                'slug'        => Str::slug($data['name']),
                'status'      => 1,
                'added_by'    => auth()->id(),
            ])->only(['id', 'name', 'slug', 'status']);

            return ApiResponse::successMessageForCreate('Unit created successfully.', $unit, Response::HTTP_CREATED);
        } catch (ValidationException $v) {
            return ApiResponse::validationError('There were validation errors. ', $v->validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (Exception $e) {
            return ApiResponse::failure('Something went wrong', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /*
     * Displays unit info by ID.
     */
    public function show(int $id): JsonResponse
    {
        try {
            $unit = Unit::where(['merchant_id' => auth()->user()->merchant->id, 'id' => $id])
                ->select('id', 'name', 'slug', 'status')
                ->firstOrFail();

            return ApiResponse::success('Unit retrieved successfully.', $unit, Response::HTTP_OK);
        } catch (ModelNotFoundException) {
            return ApiResponse::failure('Unit not found.', Response::HTTP_NOT_FOUND);
        } catch (Exception $e) {
            return ApiResponse::failure('Something went wrong', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /*
     * Updates unit details.
     */
    public function update(UnitRequest $request, int $id): JsonResponse
    {
        try {
            $request->validated();

            $unit = Unit::where(['merchant_id' => auth()->user()->merchant->id, 'id' => $id])->firstOrFail();

            $unit->fill([
                'name'     => $request->name,
                'slug'     => Str::slug($request->name),
                'added_by' => auth()->id(),
            ]);

            $unit->save();

            $updatedUnit = $unit->only(['id', 'name', 'slug', 'status']);

            return ApiResponse::success('Unit updated successfully.', $updatedUnit, Response::HTTP_OK);
        } catch (ModelNotFoundException) {
            return ApiResponse::failure('Unit not found.', Response::HTTP_NOT_FOUND);
        } catch (ValidationException $v) {
            return ApiResponse::validationError('There were validation errors. ', $v->validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (Exception $e) {
            return ApiResponse::failure('Something went wrong', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /*
     * Changes the active status of a unit.
     */
    public function status(int $id): JsonResponse
    {
        try {
            $unit = Unit::where(['merchant_id' => auth()->user()->merchant->id, 'id' => $id])
                ->select(['id', 'name', 'slug', 'status'])
                ->firstOrFail();

            $unit->update(['status' => $unit->status == '1' ? '0' : '1']);

            return ApiResponse::success('Status updated successfully.', $unit, Response::HTTP_OK);
        } catch (ModelNotFoundException) {
            return ApiResponse::failure('unit not found', Response::HTTP_NOT_FOUND);
        } catch (ValidationException $v) {
            return ApiResponse::validationError('There were validation errors.', $v->validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (Exception $e) {
            return ApiResponse::failure('Something went wrong', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /*
     * Deletes a unit by ID.
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $unit = Unit::where(['merchant_id' => auth()->user()->merchant->id, 'id' => $id])->firstOrFail();

            if ($unit->products()->count() > 0) {
                return ApiResponse::failure('Cannot delete unit. It has associated products.', Response::HTTP_FORBIDDEN);
            }

            $unit->delete();

            return ApiResponse::success('Unit deleted successfully.', Response::HTTP_OK);
        } catch (ModelNotFoundException) {
            return ApiResponse::failure('unit not found', Response::HTTP_NOT_FOUND);
        } catch (Exception $e) {
            return ApiResponse::failure('Something went wrong', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
