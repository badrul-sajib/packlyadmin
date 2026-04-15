<?php

namespace Modules\Api\V1\Ecommerce\Unit\Repositories;

use Modules\Api\V1\Ecommerce\Unit\Models\Unit;
use Modules\Api\V1\Ecommerce\Unit\Repositories\Contracts\UnitRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class UnitRepository implements UnitRepositoryInterface
{
    protected $model;

    public function __construct(Unit $model)
    {
        $this->model = $model;
    }

    public function all(): Collection
    {
        return $this->model->all();
    }

    public function paginate(int $perPage = 15)
    {
        return $this->model->paginate($perPage);
    }

    public function find(int $id): ?Model
    {
        return $this->model->findOrFail($id);
    }

    public function create(array $data): Model
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): Model
    {
        $record = $this->find($id);
        $record->update($data);
        return $record->refresh();
    }

    public function delete(int $id): bool
    {
        return $this->find($id)->delete();
    }
}
