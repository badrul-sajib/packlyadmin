<?php

namespace Modules\Api\V1\Merchant\Campaign\Repositories;

// use Modules\Api\V1\Merchant\Campaign\Models\Campaign;
use App\Enums\CampaignStatus;
use App\Models\Campaign\Campaign;
use Modules\Api\V1\Merchant\Campaign\Repositories\Contracts\CampaignRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class CampaignRepository implements CampaignRepositoryInterface
{
    protected $model;

    public function __construct(Campaign $model)
    {
        $this->model = $model;
    }

    public function activeAll(): Collection
    {
        return $this->model->where('status', CampaignStatus::OPEN_FOR_PRIME_VIEW_REQUEST->value)->orderByDesc('created_at')->get();
    }

    public function find(int $id): ?Model
    {
        return $this->model->findOrFail($id);
    }
}
