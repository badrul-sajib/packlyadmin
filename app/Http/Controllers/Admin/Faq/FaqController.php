<?php

namespace App\Http\Controllers\Admin\Faq;

use App\Actions\FetchFaq;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\FaqRequest;
use App\Models\Page\Faq;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Throwable;

class FaqController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:faq-list', ['only' => ['index']]);
        $this->middleware('permission:faq-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:faq-update', ['only' => ['edit', 'update']]);
        $this->middleware('permission:faq-delete', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @throws Throwable
     */
    public function index(Request $request)
    {
        $faqs = (new FetchFaq)->execute($request);

        if ($request->ajax()) {
            return view('components.faq.table', ['entity' => $faqs])->render();
        }

        return view('Admin::faqs.index', compact('faqs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('Admin::faqs.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(FaqRequest $request)
    {
        try {
            $faq = Faq::create([
                'question' => $request->question,
                'answer'   => $request->answer,
            ]);

            return success('FAQ created successfully', $faq);
        } catch (Throwable $th) {
            return failure('Failed to create FAQ');
        }
    }

    public function status(int $id)
    {
        try {
            $faq         = Faq::findOrFail($id);
            $faq->status = ! $faq->status;
            $faq->save();

            return response()->json(['message' => 'FAQ status updated successfully']);
        } catch (ModelNotFoundException) {
            return response()->json(['message' => 'FAQ not found'], 404);
        } catch (Exception $e) {
            return response()->json(['message' => 'Failed to update FAQ'], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Faq $faq)
    {
        return view('Admin::faqs.edit', compact('faq'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(FaqRequest $request, Faq $faq)
    {
        try {
            $faq->update([
                'question' => $request->question,
                'answer'   => $request->answer,
            ]);

            return success('FAQ updated successfully', $faq);
        } catch (Throwable $th) {
            return failure('Failed to update FAQ: ');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Faq $faq)
    {
        try {
            $faq->delete();

            return success('FAQ deleted successfully');
        } catch (Throwable $th) {
            return failure('Failed to delete FAQ: ');
        }
    }
}
