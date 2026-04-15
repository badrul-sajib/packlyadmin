<?php

namespace App\Http\Controllers\Admin\Message;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\MessageRequest;
use App\Models\Help\Message;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Throwable;

class MessageController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:help-request-list')->only('index');
        $this->middleware('permission:help-request-delete')->only('destroy');
        $this->middleware('permission:help-request-update')->only('update');
    }

    /**
     * Display a listing of the resource.
     *
     * @throws Throwable
     */
    public function index(Request $request)
    {
        $search  = $request->input('search', '');
        $perPage = $request->perPage ?? 10;
        $status  = $request->status  ?? null;
        $page    = $request->page    ?? 1;

        $messages = Message::with(['merchant', 'merchant.userRelation'])
            ->when($search, function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('phone', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%");
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page)
            ->withQueryString();

        if ($request->ajax()) {
            return view('components.help-requests.table', ['messages' => $messages])->render();
        }

        return view('backend.pages.help_requests.index', compact('messages'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(MessageRequest $request, int $id)
    {
        $request->validated();

        $shopUpdate = Message::findOrFail($id);

        $shopUpdate->update([
            'status' => $request->status,
        ]);

        $shopUpdate->merchant->sendNotification(
            'Help Request Resolved',
            'Your help request has been resolved!',
        );

        return response()->json(['message' => 'Help request status updated to ' . $request->status . '!']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        try {
            $message = Message::findOrFail($id);
            $message->delete();

            return redirect()->back()->with('success', 'Message deleted successfully');
        } catch (ModelNotFoundException $th) {
            return redirect()->back()->with('error', 'Message not found');
        }
    }
}
