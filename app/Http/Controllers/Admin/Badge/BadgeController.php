<?php

namespace App\Http\Controllers\Admin\Badge;

use App\Enums\BadgeType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BadgeRequest;
use App\Services\Badge\BadgeServices;
use Illuminate\Http\Request;

use function success;

class BadgeController extends Controller
{
    public function __construct(
        private readonly BadgeServices $badgeService
    ) {
        $this->middleware('permission:badge-list')->only('index', 'ajaxBadges');
        $this->middleware('permission:badge-create')->only(['create', 'store']);
        $this->middleware('permission:badge-show')->only('show');
        $this->middleware('permission:badge-update')->only(['edit', 'update']);
        $this->middleware('permission:badge-delete')->only('destroy');
    }

    /**
     * Display a listing of the sliders.
     */
    public function index(Request $request)
    {
        $badges = $this->badgeService->getAll($request);

        return customView(['ajax' => 'Admin::badges.table', 'default' => 'Admin::badges.index'], ['entity' => $badges]);
    }

    /**
     * Store a newly created slider in storage.
     */
    public function store(BadgeRequest $request)
    {
        $badge = $this->badgeService->create($request->validated());

        return success('Badge created successfully!', $badge);
    }

    public function create(Request $request)
    {
        $badge_products = [];
        if ($request->badge_id) {
            $badge_products = $this->badgeService->getBadgeById($request->badge_id)->badge_products;
        }

        return view('Admin::badges.create', compact('badge_products'));
    }

    public function show(int $id)
    {
        $badge = $this->badgeService->getBadgeById($id);

        return view('Admin::badges.show', compact('badge'));
    }

    /**
     * Show the form for editing the specified slider.
     */
    public function edit(int $id)
    {
        $badge      = $this->badgeService->getBadgeById($id);
        $badgeTypes = BadgeType::toArray();

        return view('Admin::badges.edit', compact('badge', 'badgeTypes'));
    }

    /**
     * Update the specified slider in storage.
     */
    public function update(BadgeRequest $request, int $id)
    {
        $this->badgeService->update($request->validated(), $id);

        return success('Badge updated successfully!');
    }

    /**
     * Remove the specified Badge from storage.
     */
    public function destroy(int $id)
    {
        $this->badgeService->delete($id);

        return response()->json(['success' => 'Badge deleted successfully!']);
    }

    public function ajaxBadges(Request $request)
    {
        $badges = $this->badgeService->getAll($request);

        return success('Badges fetched successfully', $badges->items());
    }
}
