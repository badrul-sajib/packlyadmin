<?php

namespace Modules\Api\V1\Merchant\Campaign\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface CampaignRepositoryInterface
{
    public function activeAll(): Collection;
    public function find(int $id): ?Model;
}
