<?php

namespace App\Traits;

use App\Enums\UserRole;
use App\Models\User\User;
use Illuminate\Support\Facades\Hash;

trait UserCheck
{
    public function checkUser($userId)
    {
        $user = User::find($userId);
        if ($user) {
            return $user;
        } else {
            return false;
        }
    }

    public function checkUserByEmailOrPhone($identifier): bool|User
    {
        $fieldType = filter_var($identifier, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';
        $user      = User::where($fieldType, $identifier)->whereIn('role', [UserRole::ADMIN, UserRole::SUPER_ADMIN])->first();
        if ($user) {
            return $user;
        }

        return false;
    }

    public function checkUserPassword(User $user, $password): bool
    {
        if (Hash::check($password, $user->password)) {
            return true;
        } else {
            return false;
        }
    }
}
