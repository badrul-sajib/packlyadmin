<?php

namespace Modules\Api\V1\Merchant\Campaign\Services;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Modules\Api\V1\Merchant\Campaign\Repositories\Contracts\CampaignRepositoryInterface;

class CampaignService
{
    public function __construct(
        protected CampaignRepositoryInterface $repository
    ) {}

    public function activeAll(): Collection
    {
        return $this->repository->activeAll();
    }

    public function find(int $id): ?Model
    {
        return $this->repository->find($id);
    }

    // Add your business logic here
}
