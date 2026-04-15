<?php

namespace App\Traits;

use App\Models\Visitor;
use App\Services\ApiResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

trait VisitorTrackable
{

    public function handleVisitor(Request $request)
    {
        $ip  = $request->ip();
        $url = $request->fullUrl();
        $blocked = Visitor::where('ip_address', $ip)
            ->where('is_blocked', true)
            ->exists();

        if ($blocked) {
            return ApiResponse::error('Access denied. Your IP is blocked.', Response::HTTP_FORBIDDEN);
        }
        $visitor = Visitor::where('ip_address', $ip)->where('url', $url)->first();
        if ($visitor && $visitor->is_blocked) {
            return ApiResponse::error('Access denied. Your IP is blocked.', Response::HTTP_FORBIDDEN);
        }


        if ($visitor) {

            $visitor->update([
                'visit_count'   => DB::raw('visit_count + 1'),
                'last_visit_at' => now(),
            ]);
        } else {
            Visitor::create([
                'ip_address'    => $ip,
                'url'           => $url,
                'visit_count'   => 1,
                'last_visit_at' => now(),
                'is_blocked'    => false,
            ]);
        }

        return null;
    }

    /**
     * Only track (optional use)
     */
    public function trackOnly(Request $request): void
    {
        Visitor::updateOrCreate(
            ['ip_address' => $request->ip()],
            [
                'url'           => $request->fullUrl(),
                'last_visit_at' => now(),
                'visit_count'   => DB::raw('visit_count + 1'),
            ]
        );
    }

    /**
     * Check blocked only
     */
    public function isBlocked(Request $request): bool
    {
        return Visitor::where('ip_address', $request->ip())
            ->where('is_blocked', true)
            ->exists();
    }
}
