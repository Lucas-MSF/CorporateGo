<?php

namespace App\Repositories;

use App\Interfaces\Repositories\UserRepositoryInterface;
use App\Models\User;

class UserRepository implements UserRepositoryInterface
{
    public function __construct(private readonly User $model)
    {
    }

    public function create(array $data): User
    {
        return $this->model->create($data);
    }
}
