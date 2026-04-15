<?php

namespace App\Auth;

use App\Enums\UserRole;
use App\Models\User\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Support\Facades\Hash;

class StaticAdminProvider implements UserProvider
{
    // Change these credentials as needed
    const ADMIN_ID       = 1;
    const ADMIN_NAME     = 'Admin';
    const ADMIN_EMAIL    = 'admin@packly.com';
    const ADMIN_PHONE    = '01700000000';
    const ADMIN_PASSWORD = 'admin123';

    private function makeAdminUser(): User
    {
        $user = new User();
        $user->forceFill([
            'id'       => self::ADMIN_ID,
            'name'     => self::ADMIN_NAME,
            'email'    => self::ADMIN_EMAIL,
            'phone'    => self::ADMIN_PHONE,
            'password' => Hash::make(self::ADMIN_PASSWORD),
            'role'     => UserRole::SUPER_ADMIN->value,
        ]);
        $user->exists = true;

        return $user;
    }

    public function retrieveById($identifier): ?Authenticatable
    {
        if ((int) $identifier === self::ADMIN_ID) {
            return $this->makeAdminUser();
        }

        return null;
    }

    public function retrieveByToken($identifier, $token): ?Authenticatable
    {
        return null;
    }

    public function updateRememberToken(Authenticatable $user, $token): void {}

    public function retrieveByCredentials(array $credentials): ?Authenticatable
    {
        $identifier = $credentials['email'] ?? $credentials['phone'] ?? $credentials['phone_mail'] ?? '';

        if ($identifier === self::ADMIN_EMAIL || $identifier === self::ADMIN_PHONE) {
            return $this->makeAdminUser();
        }

        return null;
    }

    public function validateCredentials(Authenticatable $user, array $credentials): bool
    {
        return Hash::check($credentials['password'], $user->getAuthPassword());
    }

    public function rehashPasswordIfRequired(Authenticatable $user, array $credentials, bool $force = false): void {}
}
