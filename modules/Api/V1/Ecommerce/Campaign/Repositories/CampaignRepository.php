<?php

namespace Modules\Api\V1\Ecommerce\Campaign\Repositories;

use App\Models\Campaign\Campaign;
use Modules\Api\V1\Ecommerce\Campaign\Repositories\Contracts\CampaignRepositoryInterface;
use Illuminate\Database\Eloquent\Model;

class CampaignRepository implements CampaignRepositoryInterface
{
    protected $model;

    public function __construct(Campaign $model)
    {
        $this->model = $model;
    }

    public function findBySlug(string $slug): ?Model
    {
        return $this->model->where('slug', $slug)->first();
    }
}
