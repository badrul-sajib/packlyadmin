<?php

namespace App\Http\Controllers\Admin\Customer;

use App\Actions\CustomerList;
use App\Http\Controllers\Controller;
use App\Models\SpamAttempt;
use App\Models\User\User;
use Illuminate\Http\Request;
use Throwable;

class CustomerController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:customer-list')->only('index');
        $this->middleware('permission:customer-show')->only('show');
    }

    /**
     * @throws Throwable
     */
    public function index(Request $request)
    {
        $customers = (new CustomerList)->execute($request);
        if ($request->ajax()) {
            return view('components.customers.table', ['entity' => $customers])->render();
        }

        return view('Admin::customers.index', compact('customers'));
    }

    public function show($id)
    {
        $user = User::customer()->findOrFail($id);
        if (! $user) {
            abort(404);
        }
        $spamAttempts = SpamAttempt::with('order')->where('user_id', $user->id)->paginate();

        return view('Admin::customers.show', compact('user', 'spamAttempts'));
    }

    public function statusUpdate(Request $request, User $user)
    {
        $request->validate([
            'status' => 'required|in:0,1',
        ]);

        $user->status = $request->status;
        $user->save();

        return redirect()->back()->with('success', 'Customer status updated successfully.');
    }
}
