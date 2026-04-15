<?php

namespace App\Http\Controllers\Admin\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function search(Request $request)
    {
        return back()->with('info', 'Search is disabled in static mode.');
    }

    public function index(Request $request)
    {
        return view('pages.dashboard.index');
    }
}
