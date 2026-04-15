<?php

namespace App\Http\Controllers\Admin\Campaign;

use App\Enums\CampaignProductStatus;
use App\Enums\CampaignStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Campaign\CampaignRequest;
use App\Models\Campaign\Campaign;
use App\Models\Campaign\CampaignProduct;
use App\Models\PrimeView\PrimeView;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class CampaignController extends Controller
{
    public function index(Request $request)
    {
        $campaigns = Campaign::with('primeViews')
            ->when(
                $request->status,
                fn($q) =>
                $q->where('status', $request->status)
            )
            ->when(
                $request->search,
                fn($q) =>
                $q->where('name', 'like', '%' . $request->search . '%')
            )
            ->latest()
            ->paginate(10)
            ->withQueryString();

        if ($request->ajax()) {
            return view('components.campaign.table', [
                'entity' => $campaigns
            ]);
        }

        return view('backend.pages.campaign.index', compact('campaigns'));
    }

    public function create()
    {
        $primeViews = PrimeView::where('status', 1)->orderByDesc('id')->get();
        return view('backend.pages.campaign.create', compact('primeViews'));
    }

    public function store(CampaignRequest $request)
    {
        return DB::transaction(function () use ($request) {
            $data = $request->only([
                'name',
                'start_title',
                'end_title',
                'start_subtitle',
                'end_subtitle',
                'slug',
                'starts_at',
                'ends_at',
                'vendor_request_start',
                'vendor_request_end',
                'visibility_rules',
            ]);

            $data['created_by'] = auth()->id();
            $data['status'] = CampaignStatus::DRAFT->value;


            $campaign = Campaign::create($data);

            if ($request->hasFile('image')) {
                $campaign->image = $request->file('image');
                $campaign->save();
            }

            if ($request->hasFile('logo')) {
                $campaign->logo = $request->file('logo');
                $campaign->save();
            }

            // normalize tiers: request->tiers is numeric indexed after CampaignRequest prepareForValidation
            $pivotData = [];
            foreach ($request->input('tiers', []) as $t) {
                // ensure prime_view_id exists
                if (empty($t['prime_view_id']))
                    continue;
                $pivotData[$t['prime_view_id']] = [
                    'discount_amount' => $t['discount_amount'] ?? null,
                    'discount_type' => $t['discount_type'] ?? null,
                    'rules' => $t['rules'] ?? null,
                ];
            }

            if (!empty($pivotData)) {
                $campaign->primeViews()->sync($pivotData);
            }

            return redirect()->route('admin.campaigns.index')->with('success', 'Campaign created successfully.');
        });
    }

    public function edit(Campaign $campaign)
    {
        $primeViews = PrimeView::where('status', 1)->orderByDesc('id')->get();

        // prepare $tiers as array of arrays with prime_view_id as key for the blade convenience
        // we will pass $tiersIndexed so edit form can populate using prime_view_id as key
        $tiersIndexed = $campaign->primeViews->map(function ($pv) {
            return [
                'prime_view_id' => $pv->id,
                'discount_amount' => $pv->pivot->discount_amount,
                'discount_type' => $pv->pivot->discount_type,
                'rules' => $pv->pivot->rules,
            ];
        })->values()->toArray();

        return view('backend.pages.campaign.edit', compact('campaign', 'primeViews', 'tiersIndexed'));
    }

    public function update(CampaignRequest $request, Campaign $campaign)
    {
        return DB::transaction(function () use ($request, $campaign) {
            $data = $request->only([
                'name',
                'start_title',
                'end_title',
                'start_subtitle',
                'end_subtitle',
                'slug',
                'starts_at',
                'ends_at',
                'vendor_request_start',
                'vendor_request_end',
                'visibility_rules',
            ]);

            $campaign->update($data);

            if ($request->hasFile('image')) {
                $campaign->image = $request->file('image');
                $campaign->save();
            }

            if ($request->hasFile('logo')) {
                $campaign->logo = $request->file('logo');
                $campaign->save();
            }

            // Build pivot
            $pivotData = collect($request->tiers)->mapWithKeys(function ($tier) {
                return [
                    $tier['prime_view_id'] => [
                        'discount_amount' => $tier['discount_amount'],
                        'discount_type' => $tier['discount_type'],
                        'rules' => $tier['rules'],
                    ]
                ];
            })->toArray();

            $campaign->primeViews()->sync($pivotData);

            return redirect()->route('admin.campaigns.index')->with('success', 'Campaign updated successfully.');
        });
    }

    public function destroy(Campaign $campaign): JsonResponse
    {
        $campaign->delete();
        return response()->json(['message' => 'Campaign deleted successfully!']);
    }

    public function statusUpdate(Request $request, Campaign $campaign)
    {
        $campaign->update(['status' => $request->status]);
        return redirect()->route('admin.campaigns.index')->with('success', 'Campaign status updated successfully.');
    }

    public function show(Request $request, Campaign $campaign)
    {
        $perPage = $request->input('perPage', 10);
        $page = $request->input('page', 1);
        $search = $request->input('search', '');
        $status = $request->input('status', null);

        $campaignProducts = CampaignProduct::where('campaign_id', $campaign->id)
            ->when($search, function ($query, $search) {
                $query->whereHas('product', function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")->orWhere('sku', 'like', "%{$search}%");
                })->orWhereHas('merchant', function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")->orWhere('phone', 'like', "%{$search}%")->orWhere('shop_name', 'like', "%{$search}%");
                })->orWhereHas('primeView', function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%");
                });
            })
            ->when($status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->orderByDesc('created_at')->paginate($perPage, ['*'], 'page', $page);

        if ($request->ajax()) {
            return view('components.campaign.request_table', ['entity' => $campaignProducts])->render();
        }

        return view('backend.pages.campaign.show', compact('campaign', 'campaignProducts'));
    }

    public function bulkUpdate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'campaign_id' => ['required', 'integer', 'exists:campaigns,id'],
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer', 'distinct', 'exists:campaign_products,id'],
            'status' => [
                'required',
                Rule::in(array_column(CampaignProductStatus::cases(), 'value')),
            ],
        ]);

        DB::transaction(function () use ($validated) {

            $campaignProducts = CampaignProduct::query()
                ->where('campaign_id', $validated['campaign_id'])
                ->whereIn('id', $validated['ids'])
                ->lockForUpdate()
                ->get();

            CampaignProduct::whereIn('id', $campaignProducts->pluck('id'))
                ->update([
                    'status' => $validated['status'],
                ]);

            $campaignProducts
                ->groupBy('prime_view_id')
                ->each(function ($items, $primeViewId) {

                    if (!$primeViewId) {
                        return;
                    }

                    $primeView = PrimeView::find($primeViewId);

                    if (!$primeView) {
                        return;
                    }

                    $primeView->products()
                        ->sync(
                            $items->pluck('product_id')->unique()->toArray()
                        );
                });
        });

        return response()->json([
            'message' => 'Campaign products status updated successfully.',
        ]);
    }
}
