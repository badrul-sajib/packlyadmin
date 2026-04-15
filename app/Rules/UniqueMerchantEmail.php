<?php

namespace App\Rules;

use Closure;
use App\Models\User\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Validation\ValidationRule;

class UniqueMerchantEmail implements ValidationRule
{
    public function __construct(private readonly int|string|null $ignoreUserId = null)
    {
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $query = DB::connection('mysql_external')
            ->table('users')
            ->where('email', $value)
            ->where('role', User::$ROLE_MERCHANT);

        if (! is_null($this->ignoreUserId)) {
            $query->where('id', '!=', $this->ignoreUserId);
        }

        $isTaken = $query->exists();

        // If the email is already taken by a merchant, fail validation.
        if ($isTaken) {
            $fail('The email is already registered.');
        }
    }
}
