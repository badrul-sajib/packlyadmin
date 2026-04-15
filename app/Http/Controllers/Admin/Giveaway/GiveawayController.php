<?php

namespace App\Http\Controllers\Admin\Giveaway;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;
use App\Enums\GiveawayStatus;
use App\Enums\UserRole;
use App\Models\Giveaway\Giveaway;
use App\Services\GiveawayService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\GiveawayRequest;

class GiveawayController extends Controller
{

    public function __construct(protected GiveawayService $giveawayService)
    {
    }

    public function index()
    {
        $perPage = request()->perPage ?? 10;
        $giveaways = Giveaway::withCount(['gifts', 'tickets', 'draws'])
                    ->when(request()->search, function ($query) {
                        $query->where('name', 'like', '%' . request()->search . '%');
                    })
                    ->latest()->paginate($perPage);

        if(request()->ajax()){
             return view('backend.pages.giveaway.table', ['entity' => $giveaways])->render();
        }

        return view('backend.pages.giveaway.index', compact('giveaways'));
    }

    public function create()
    {
        return view('backend.pages.giveaway.create');
    }

    public function store(GiveawayRequest $request)
    {
        try {

            $this->giveawayService->createGiveaway(
                $request->only(['name', 'description', 'start_at', 'end_at']),
                $request->input('gifts')
            );

            return redirect()->route('admin.giveaway.index')->with('success', 'Giveaway created successfully.');
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }   
    }

    public function edit(Giveaway $giveaway)
    {
        $giveaway->load('gifts');
        return view('backend.pages.giveaway.edit', compact('giveaway'));
    }

    public function update(GiveawayRequest $request, Giveaway $giveaway)
    {
        try {
            $this->giveawayService->updateGiveaway(
                $giveaway,
                $request->only(['name', 'description', 'start_at', 'end_at']),
                $request->input('gifts')
            );

            return redirect()->route('admin.giveaway.index')->with('success', 'Giveaway updated successfully.');
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }

    public function destroy(Giveaway $giveaway)
    {
        try {
            $this->giveawayService->deleteGiveaway($giveaway);
            return back()->with('success', 'Giveaway deleted successfully.');
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }

    public function show(Giveaway $giveaway)
    {

        $perPage = request()->perPage ?? 10;
        $orderTickets = $giveaway->tickets()->with('order','user')->paginate($perPage);

        if(request()->ajax()){
            return view('backend.pages.giveaway.order_tickets', ['entity' => $orderTickets])->render();
        }

        $giveaway->load(['gifts', 'draws.user', 'draws.gift']);
        return view('backend.pages.giveaway.show', compact('giveaway','orderTickets'));
    }


    public function draw(Giveaway $giveaway)
    {
        try {
            $this->giveawayService->performDraw($giveaway);
            return back()->with('success', 'Draw performed successfully!');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function spinner(Giveaway $giveaway)
    {
        if ($giveaway->status !== 'drawn') {
            return redirect()->route('admin.giveaway.show', $giveaway)->with('error', 'Giveaway not drawn yet.');
        }

        $giveaway->load(['draws.ticket.user', 'draws.gift']);
        
        // Get some random tickets for the animation effect (e.g. 50 tickets)
        // We include the winners in this list or ensure they are inserted at the end
        
        // Simplified: just pass the winners and let the frontend animate "searching"
        // Or pass a list of tickets.
        
        $winners = $giveaway->draws;
        
        return view('backend.pages.giveaway.spinner', compact('giveaway', 'winners'));
    }

    public function updateStatus(Request $request, Giveaway $giveaway): JsonResponse
    {
        $user = auth()->user();

        $validated = $request->validate([
            'status' => ['required', Rule::in(GiveawayStatus::values())],
        ]);

        $current = GiveawayStatus::from($giveaway->status);
        $target = GiveawayStatus::from($validated['status']);

        /**
         * Check if the transition is valid
         * If not, return an error response
         */
        // if (! $current->canTransitionTo($target)) {
        //     return response()->json([
        //         'message' => 'Invalid status transition.',
        //         'current' => $current->value,
        //         'requested' => $target->value,
        //     ], 422);
        // }

                
        if ($target === GiveawayStatus::Active) {
            $alreadyActiveExists = Giveaway::where('status', GiveawayStatus::Active->value)
                ->where('id', '!=', $giveaway->id)
                ->exists();

            if ($alreadyActiveExists) {
                return response()->json([
                    'message' => 'Only one giveaway can be active at a time. Please end or cancel the currently active giveaway first.',
                ], 422);
            }
        }

        $giveaway->update([
            'status' => $target->value,
        ]);

        return response()->json([
            'message' => 'Giveaway status updated successfully.',
            'status' => $target->value,
            'label' => $target->label(),
        ]);
    }
}
