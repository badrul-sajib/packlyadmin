<?php

namespace Modules\Api\V1\Ecommerce\Campaign\Services;
use Illuminate\Database\Eloquent\Model;
use Modules\Api\V1\Ecommerce\Campaign\Repositories\Contracts\CampaignRepositoryInterface;

class CampaignService
{
    public function __construct(
        protected CampaignRepositoryInterface $repository
    ) {}

    public function findBySlug(string $slug): ?Model
    {
        return $this->repository->findBySlug($slug);
    }
}
