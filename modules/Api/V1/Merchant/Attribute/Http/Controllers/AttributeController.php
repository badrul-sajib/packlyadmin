<?php

namespace Modules\Api\V1\Merchant\Attribute\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Api\V1\Merchant\Attribute\Http\Requests\AttributeRequest;
use App\Models\Attribute\Attribute;
use App\Models\Attribute\AttributeOption;
use App\Models\Attribute\VariationAttribute;
use App\Services\ApiResponse;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class AttributeController extends Controller
{
    public function __construct()
    {
        $this->middleware('shop.permission:show-attribute')->only('index', 'show');
        $this->middleware('shop.permission:create-attribute')->only('store');
        $this->middleware('shop.permission:update-attribute')->only('update');
        $this->middleware('shop.permission:delete-attribute')->only('destroy');
    }

    /*
     * Returns a list of all attributes.
     */
    public function index(): JsonResponse
    {
        try {
            // Fetch all attributes with their options
            $attributes = Attribute::where('merchant_id', Auth::user()->merchant->id)
                ->orderBy('id', 'desc')
                ->with('options')
                ->active()
                ->get();

            $allList = [];

            // Loop through each attribute
            foreach ($attributes as $attribute) {
                // Prepare attribute data
                $attributeData = [
                    'id'      => $attribute->id,
                    'name'    => $attribute->name,
                    'slug'    => $attribute->slug,
                    'options' => $attribute->options->map(function ($option) {
                        return [
                            'id'              => $option->id,
                            'attribute_value' => $option->attribute_value,
                        ];
                    }),
                ];

                // Add to list
                $allList[] = $attributeData;
            }

            // Return the response as JSON
            return ApiResponse::success('All Attributes retrieved successfully', $allList, Response::HTTP_OK);
        } catch (Exception) {
            // Handle any exceptions
            return ApiResponse::error('An error occurred while fetching attributes.', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /*
     * Adds a new attribute and values.
     */
    public function store(AttributeRequest $request): JsonResponse
    {
        try {
            $request->validated();

            DB::beginTransaction();

            $attribute = Attribute::create([
                'name'        => $request->name,
                'merchant_id' => Auth::user()->merchant->id,
                'slug'        => Str::slug($request->name),
                'added_by'    => Auth::id(),
            ]);

            $skippedValues = [];

            if (! empty($request->attribute_values)) {
                $attributeValues = explode(',', $request->attribute_values);

                foreach ($attributeValues as $value) {

                    // check empty values
                    if (empty($value)) {
                        throw new Exception('Attribute value cannot be empty');
                    }

                    $checkAttributeOption = AttributeOption::where('merchant_id', Auth::user()->merchant->id)
                        ->where('attribute_value', $value)
                        ->exists();

                    if (! $checkAttributeOption) {
                        AttributeOption::create([
                            'attribute_value' => $value,
                            'merchant_id'     => Auth::user()->merchant->id,
                            'slug'            => Str::slug($value),
                            'attribute_id'    => $attribute->id,
                            'added_by'        => Auth::id(),
                        ]);
                    } else {
                        $skippedValues[] = $value;
                    }
                }
            }

            DB::commit();

            $message = 'Attribute created successfully';
            if (! empty($skippedValues)) {
                $message .= ', but the following values already exist: ' . implode(', ', $skippedValues);
            }

            return ApiResponse::successMessageForCreate($message, [$attribute], Response::HTTP_CREATED);
        } catch (ValidationException $v) {
            return ApiResponse::validationError('There were validation errors.', $v->validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (Exception $e) {
            return ApiResponse::failure('Attribute not found.', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /*
     * Shows a single attribute by slug.
     */
    public function show(string $slug): JsonResponse
    {
        try {
            $attribute = Attribute::where('merchant_id', Auth::user()->merchant->id)
                ->where('slug', $slug)
                ->with('options.user')
                ->firstOrFail();

            $formattedOptions = $attribute->options->map(function ($option) {
                return $option->formatted_option;
            });

            return response()->json(
                [
                    'data' => [
                        'id'         => $attribute->id,
                        'name'       => $attribute->name,
                        'slug'       => $attribute->slug,
                        'status'     => $attribute->status,
                        'added_by'   => $attribute->added_by,
                        'created_at' => Carbon::parse($attribute->created_at)->format('Y-m-d H:i'),
                        'updated_at' => Carbon::parse($attribute->updated_at)->format('Y-m-d H:i'),
                        'options'    => $formattedOptions,
                    ],
                    'message' => 'Show details',
                ],
                Response::HTTP_OK,
            );
        } catch (ModelNotFoundException) {
            return ApiResponse::failure('Attribute not found.', Response::HTTP_NOT_FOUND);
        } catch (Exception $e) {
            return ApiResponse::failure('Attribute not found.', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /*
     * Updates an attribute by slug.
     */
    public function update(AttributeRequest $request, string $slug): JsonResponse
    {
        try {
            $attribute = Attribute::where('slug', $slug)
                ->where('merchant_id', Auth::user()->merchant->id)
                ->firstOrFail();

            $request->validated();

            if (is_array($request->attribute_values)) {

                // check duplicate values in the array
                $values       = array_column($request->attribute_values, 'value');
                $uniqueValues = array_unique($values);
                if (count($values) !== count($uniqueValues)) {
                    return ApiResponse::failure('Duplicate values are not allowed.', Response::HTTP_CONFLICT);
                }

                // check empty values in the array
                foreach ($request->attribute_values as $option) {
                    if (empty($option['value'])) {
                        throw new Exception('Attribute value cannot be empty');
                    }
                }
            }

            DB::beginTransaction();

            $attribute->update([
                'name' => $request->name,
            ]);

            if (is_array($request->attribute_values)) {

                foreach ($request->attribute_values as $option) {

                    if (isset($option['id'])) {
                        $existingOption = AttributeOption::where('id', $option['id'])
                            ->where('attribute_id', $attribute->id)
                            ->first();

                        if ($existingOption) {
                            $existingOption->update([
                                'attribute_value' => $option['value'],
                            ]);
                        }
                    } else {
                        AttributeOption::create([
                            'merchant_id'     => Auth::user()->merchant->id,
                            'attribute_id'    => $attribute->id,
                            'attribute_value' => $option['value'],
                            'slug'            => Str::slug($option['value']),
                            'added_by'        => Auth::id(),
                        ]);
                    }
                }
            }

            if (is_array($request->deleted_options)) {
                // Check for used options in the variation_attributes table
                $usedOptions = VariationAttribute::with('attributeOption')
                    ->whereIn('attribute_option_id', $request->deleted_options)
                    ->where('attribute_id', $attribute->id)
                    ->get();
                if ($usedOptions->isNotEmpty()) {
                    // Collect the names of used options for the error message
                    $optionNames = $usedOptions->pluck('attributeOption.attribute_value')->toArray();
                    $optionNames = array_unique($optionNames);

                    return ApiResponse::error('These values are in use and cannot be deleted : ' . implode(', ', $optionNames), Response::HTTP_CONFLICT);
                } else {
                    // Delete the options if they are not used
                    AttributeOption::whereIn('id', $request->deleted_options)
                        ->where('attribute_id', $attribute->id)
                        ->delete();
                }
            }

            DB::commit();

            return ApiResponse::success('Attribute updated successfully', [$attribute], Response::HTTP_OK);
        } catch (ModelNotFoundException) {
            return ApiResponse::failure('Attribute not found.', Response::HTTP_NOT_FOUND);
        } catch (ValidationException $v) {
            return ApiResponse::validationError('There were validation errors. ', $v->validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (Exception $e) {
            return ApiResponse::failure('Attribute not found.', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /*
     * Deletes an attribute by slug.
     */
    public function destroy(string $slug): JsonResponse
    {
        try {
            $attribute = Attribute::where('merchant_id', auth()->user()->merchant->id)
                ->where('slug', $slug)
                ->firstOrFail();

            if ($attribute->variationAttributes()->exists()) {
                return ApiResponse::error('This attribute is in use and cannot be deleted.', Response::HTTP_CONFLICT);
            }

            // check relations
            $attribute->delete();

            return ApiResponse::success('Deleted successfully.', Response::HTTP_OK);
        } catch (ModelNotFoundException $m) {
            return ApiResponse::failure('Attribute not found.', Response::HTTP_NOT_FOUND);
        } catch (Exception $e) {
            return ApiResponse::failure('Attribute not found.', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
