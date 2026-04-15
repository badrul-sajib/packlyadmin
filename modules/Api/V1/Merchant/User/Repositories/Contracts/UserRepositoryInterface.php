<?php

namespace Modules\Api\V1\Merchant\User\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface UserRepositoryInterface
{
    public function find(int $id): ?Model;
    public function create(array $data): Model;
    public function update(int $id, array $data): Model;
}
