<?php

namespace App\Models\Chat;

use App\Models\User\User;
use App\Traits\HasTimezone;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Conversation extends Model
{
    use HasTimezone;

    protected $connection = 'mysql_internal';

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'conversation_users');
    }

    public function chats(): HasMany
    {
        return $this->hasMany(Chat::class);
    }
}
