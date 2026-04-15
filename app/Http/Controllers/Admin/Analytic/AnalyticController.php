<?php

namespace App\Http\Controllers\Admin\Analytic;

use App\Http\Controllers\Controller;
use App\Models\Shop\ShopSetting;
use Illuminate\Http\Request;
use Throwable;

class AnalyticController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:analytics-update')->only('index');
    }

    /**
     * @throws Throwable
     */
    public function index(Request $request)
    {
        $settings = ShopSetting::where('group_name', 'Analytics')->get();

        if ($request->ajax()) {
            return view('components.setting.form', compact('settings'))->render();
        }

        return view('Admin::analytics.index', compact('settings'));
    }
}
