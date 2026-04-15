<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\DB;
use Illuminate\Translation\PotentiallyTranslatedString;

class UniqueForMerchant implements ValidationRule
{
    protected string $table;

    protected string $column;

    protected ?string $ignoreColumn;

    protected mixed $ignoreValue;

    public function __construct(string $table, string $column = 'name', ?string $ignoreColumn = null, mixed $ignoreValue = null)
    {
        $this->table = $table;
        $this->column = $column;
        $this->ignoreColumn = $ignoreColumn;
        $this->ignoreValue = $ignoreValue;
    }

    /**
     * Validate the attribute.
     *
     * @param  string                                       $attribute
     * @param  mixed                                        $value
     * @param  Closure(string): PotentiallyTranslatedString $fail
     * @return void
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $merchantId = auth()->user()->merchant?->id;

        $query = DB::table($this->table)
            ->where($this->column, $value)
            ->where('merchant_id', $merchantId);

        if ($this->ignoreColumn && $this->ignoreValue) {
            $query->where($this->ignoreColumn, '!=', $this->ignoreValue);
        }

        if ($query->exists()) {
            $fail("The $attribute has already been taken.");
        }
    }
}
