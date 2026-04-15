<?php

namespace App\Http\Controllers\Admin\User;

use Throwable;
use App\Enums\UserRole;
use App\Models\User\User;
use App\Http\Controllers\Controller;

class UserController extends Controller
{

    public function ajaxUsers()
    {
        $search = request()->input('search', '');
        $users =  User::query()
            ->where('role', UserRole::ADMIN->value)
            ->when($search, function ($query) use ($search) {
                $query->whereAny(['name', 'phone', 'id'], 'like', '%' . $search . '%');
            })
            ->limit(20)->select('id', 'name', 'phone')->get();

        return success('User fetched successfully', $users);
    }
    
}
