<?php

namespace App\Http\Controllers\Admin\Visitor;

use App\Http\Controllers\Controller;
use App\Models\Visitor;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class VisitorController extends Controller
{
    public function index(Request $request)
    {
        $perPage = (int) $request->query('perPage', 25);
        $perPage = $perPage > 0 ? min($perPage, 100) : 25;

        $query = Visitor::query();

        if ($search = trim($request->query('search', ''))) {
            $query->where(function ($builder) use ($search) {
                $builder->where('ip_address', 'like', "%{$search}%")
                    ->orWhere('url', 'like', "%{$search}%");
            });
        }

        if ($blocked = $request->query('blocked')) {
            if ($blocked === 'blocked') {
                $query->where('is_blocked', true);
            } elseif ($blocked === 'allowed') {
                $query->where('is_blocked', false);
            }
        }

        $visitors = $query->orderByDesc('last_visit_at')->paginate($perPage)->withQueryString();

        if ($request->ajax()) {
            return view('components.visitors.table', ['entity' => $visitors])->render();
        }

        return view('Admin::visitors.index', compact('visitors'));
    }

    public function toggleBlock(Request $request, Visitor $visitor)
    {
        $visitor->is_blocked = ! $visitor->is_blocked;
        $visitor->save();

        $message = $visitor->is_blocked ? 'IP blocked successfully' : 'IP unblocked successfully';

        if ($request->expectsJson()) {
            return response()->json([
                'message' => $message,
                'blocked' => $visitor->is_blocked,
            ], HttpResponse::HTTP_OK);
        }

        return redirect()->back()->with('success', $message);
    }
}
