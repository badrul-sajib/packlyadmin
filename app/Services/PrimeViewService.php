<?php

namespace App\Services;

use App\Models\PrimeView\PrimeView;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Spatie\Activitylog\Models\Activity;

class PrimeViewService
{
    // --------------- Prime View Wev Services ---------------#
    public function getPrimeViews($request): LengthAwarePaginator
    {
        $perPage = $request->input('perPage', 10);
        $page    = $request->input('page', 1);
        $search  = $request->input('search', '');

        return PrimeView::query()
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->with('products')
            ->orderByDesc('id')
            ->paginate($perPage, ['*'], 'page', $page);
    }

    public function getPrimeViewAll(): Collection
    {
        return PrimeView::select('id', 'name')->orderBy('order')->get();
    }

    public function storePrimeView($data): PrimeView
    {
        $primeView                 = new PrimeView;
        $primeView->name           = $data['name'];
        $primeView->status         = $data['status'];
        $primeView->show_on_sticky = $data['show_on_sticky'];
        $primeView->explore_item   = $data['explore_item'];
        $primeView->start_date     = $data['start_date'];
        $primeView->end_date       = $data['end_date'];
        $primeView->slug           = Str::slug($data['name']);
        $primeView->save();

        if (! empty($data['menu_icon']) || ! empty($data['background'])) {
            $primeView->menu_icon      = $data['menu_icon']  ?? null;
            $primeView->background     = $data['background'] ?? null;
            $primeView->save();
        }

        activity()
            ->useLog('prime-view-create')
            ->event('created')
            ->performedOn($primeView)
            ->causedBy(auth()->user())
            ->withProperties([
                'created_prime_view' => $primeView->name,
            ])
            ->log('Prime View created by '.auth()->user()->name);

        return $primeView;
    }

    public function updatePrimeView(int $id, $data)
    {
        $primeView = PrimeView::find($id);

        $primeView->fill($data);

        $changes = getModelChanges($primeView);

        if (! blank($changes['new'])) {
            activity()
                ->useLog('prime-view-update')
                ->event('updated')
                ->performedOn($primeView)
                ->causedBy(auth()->user())
                ->withProperties([
                    'changes' => $changes,
                ])
                ->log('Prime View updated by '.auth()->user()->name);
        }

        $primeView->save();

        return $primeView;
    }

    public function updateOrder($orderData): bool
    {
        foreach ($orderData as $index => $id) {
            PrimeView::where('id', $id)->update(['order' => $index + 1]);
        }

        return true;
    }

    /**
     * @throws Exception
     */
    public function deletePrimeView(int $id)
    {
        $primeView = PrimeView::find($id);

        if ($primeView->primeViewProducts()->count() > 0) {
            throw new Exception('Prime View has products');
        }
        $deletedData = [
            'id'   => $primeView->id,
            'name' => $primeView->name,
        ];

        $subjectType = get_class($primeView);

        $primeView->delete();
        activity()
            ->useLog('prime-view-delete')
            ->event('deleted')
            ->causedBy(auth()->user())
            ->withProperties([
                'deleted_prime_view' => $deletedData['name'],
            ])
            ->tap(function (Activity $activity) use ($subjectType, $deletedData) {
                $activity->subject_type = $subjectType;
                $activity->subject_id   = $deletedData['id'];
            })
            ->log('Prime View deleted by '.auth()->user()->name);

        return $primeView;
    }
}
