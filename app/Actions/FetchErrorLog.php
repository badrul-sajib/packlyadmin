<?php

namespace App\Actions;

use App\Models\ErrorLog\ErrorLog;

class FetchErrorLog
{
    /**
     * For admin listing with filters (search + status_code)
     */
    public function execute($request)
    {
        $statusCode  = $request->status_code ?? '';
        $search      = $request->message     ?? '';
        $source      = $request->source      ?? '';
        $environment = $request->environment ?? '';
        $perPage     = $request->per_page    ?? 10;

        return ErrorLog::query()
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('message', 'like', "%{$search}%")
                        ->orWhere('endpoint', 'like', "%{$search}%")
                        ->orWhere('current_route', 'like', "%{$search}%")
                        ->orWhere('user_agent', 'like', "%{$search}%");
                });
            })
            ->when($statusCode, function ($query) use ($statusCode) {
                $query->where('status_code', $statusCode);
            })
            ->when($source, function ($query) use ($source) {
                $query->where('source', $source);
            })
            ->when($environment, function ($query) use ($environment) {
                $query->where('environment', $environment);
            })
            ->select('id', 'source', 'client_type', 'status_code', 'endpoint', 'message', 'occurrence_count', 'last_occurred_at')
            ->latest('last_occurred_at')
            ->paginate($perPage)
            ->withQueryString();
    }
}
