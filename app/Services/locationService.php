<?php

namespace App\Services;

use App\Models\Order\Location;

class locationService
{
    public function getLocations($request)
    {
        $search      = $request->search;
        $location_id = $request->input('location_id', '');
        $type        = $request->input('type', $location_id ? '' : 'city');
        $perPage     = $request->input('perPage', 10);
        $page        = $request->input('page', 1);

        return Location::query()
            ->with(['parent.parent', 'children'])
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->when($type, function ($query) use ($type) {
                $query->where('type', $type);
            })
            ->when($location_id, function ($query) use ($location_id) {
                $query->where('parent_id', $location_id);
            })
            ->paginate($perPage, ['*'], 'page', $page)->withQueryString();
    }

    public function createLocation($request)
    {
        $data            = $request->validated();
        $parent_location = Location::find($request->parent_id);
        if ($request->parent_id) {
            $data['type'] = $parent_location->type == 'division' ? 'district' : 'city';
        } else {
            $data['parent_id'] = null;
            $data['type']      = 'division';
        }

        return Location::create($data);
    }

    public function updateLocation($request, $location)
    {
        $data            = $request->validated();
        $parent_location = Location::find($request->parent_id);
        if ($request->parent_id) {
            $data['type'] = $parent_location->type == 'division' ? 'district' : 'city';
        } else {
            $data['parent_id'] = null;
            $data['type']      = 'division';
        }

        return $location->update($data);
    }

    public function getLocationById($id)
    {
        return Location::find($id);
    }

    public function getSearchLocations($request)
    {
        $search = $request->search;
        $type   = $request->input('type', 'division');

        return Location::query()
            ->where('parent_id', null)
            ->with('children')
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->when($type, function ($query) use ($type) {
                $query->where('type', $type);
            })
            ->get();

    }

    public function getDivisions()
    {
        return Location::whereNull('parent_id')->with('children')->get();
    }
}
