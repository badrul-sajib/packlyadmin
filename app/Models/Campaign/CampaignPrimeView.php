<?php

namespace App\Models\Campaign;

use App\Traits\HasTimezone;
use Illuminate\Database\Eloquent\Model;

class CampaignPrimeView extends Model
{
    use HasTimezone;

    protected $connection = 'mysql_internal';

    protected $table = 'campaign_prime_view';

    protected $guarded = [];
}
