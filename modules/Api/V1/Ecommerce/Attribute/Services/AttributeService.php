<?php

namespace Modules\Api\V1\Ecommerce\Attribute\Services;

use App\Models\Attribute\Attribute;
use Modules\Api\V1\Ecommerce\Attribute\Repositories\Contracts\AttributeRepositoryInterface;

class AttributeService
{
    public function __construct(
        protected AttributeRepositoryInterface $repository
    ) {}

    /**
     * Get all attributes paginated.
     *
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function paginate(int $perPage = 15)
    {
        return Attribute::with(['options' => function ($q) {
            $q->where('added_by', 0)
                ->where('status', 1);
        }])
            ->where('added_by', 0)
            ->where('status', 1)
            ->paginate($perPage);
    }

    public function find(int $id)
    {
        return $this->repository->find($id);
    }

    public function create(array $data)
    {
        return $this->repository->create($data);
    }

    public function update(int $id, array $data)
    {
        return $this->repository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->repository->delete($id);
    }

    // Add your business logic here
}
