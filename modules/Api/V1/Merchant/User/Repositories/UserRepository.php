<?php

namespace Modules\Api\V1\Merchant\User\Repositories;

use Modules\Api\V1\Merchant\User\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Database\Eloquent\Model;

class UserRepository implements UserRepositoryInterface
{
    protected $model;

    public function __construct(\App\Models\User\User $model)
    {
        $this->model = $model;
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
        $user = $this->find($id);
        $user->fill($data);
        $user->save();

        return $user->refresh();
    }
}
