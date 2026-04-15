<?php

namespace Modules\Api\V1\Ecommerce\Attribute\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface AttributeRepositoryInterface
{
    public function all(): Collection;
    public function paginate(int $perPage = 15);
    public function find(int $id): ?Model;
    public function create(array $data): Model;
    public function update(int $id, array $data): Model;
    public function delete(int $id): bool;
}
