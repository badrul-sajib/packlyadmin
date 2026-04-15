<?php

namespace Modules\Api\V1\Ecommerce\Campaign\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface CampaignRepositoryInterface
{
    public function findBySlug(string $slug): ?Model;
}
