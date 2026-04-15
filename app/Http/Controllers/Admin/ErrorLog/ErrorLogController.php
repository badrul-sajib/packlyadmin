<?php

namespace App\Http\Controllers\Admin\ErrorLog;

use App\Actions\FetchErrorLog;
use App\Exports\ErrorLogsExport;
use App\Http\Controllers\Controller;
use App\Models\ErrorLog\ErrorLog;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ErrorLogController extends Controller
{
    public function index(Request $request)
    {
        $logs = (new FetchErrorLog)->execute($request);
        if ($request->ajax()) {
            return view('components.error_logs.table', ['entity' => $logs])->render();
        }

        return view('Admin::error_logs.index', compact('logs'));
    }

    public function export(Request $request)
    {
        $fileName = now()->toDateString().'-error-logs.xlsx';

        return Excel::download(new ErrorLogsExport($request), $fileName);
    }

    public function show($id)
    {
        $errorLog = ErrorLog::findOrFail($id);

        return view('Admin::error_logs.show', compact('errorLog'));
    }

    public function destroy($id)
    {
        $errorLog = ErrorLog::findOrFail($id);
        $errorLog->delete();

        return redirect()->back()->with('success', 'Error log deleted successfully');
    }

    public function destroyAll()
    {
        ErrorLog::query()->delete();

        return redirect()->back()->with('success', 'All error logs deleted successfully');
    }
}
