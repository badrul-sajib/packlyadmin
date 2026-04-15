<?php

namespace Modules\Api\V1\Ecommerce\Faq\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Api\V1\Ecommerce\Faq\Http\Resources\FaqResource;
use App\Models\Page\Faq;
use Exception;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FaqController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $search   = $request->get('search');
            $per_page = $request->get('per_page', 10);

            $faqs = Faq::where('status', true)
                ->when($search, function ($query, $search) {
                    return $query->whereAny(['question', 'answer'], 'like', '%'.$search.'%');
                })
                ->paginate($per_page);

            return formatPagination('Frequently Asked Questions showed successfully', FaqResource::collection($faqs));
        } catch (Exception $e) {
            return failure('Failed to show frequently asked questions', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
