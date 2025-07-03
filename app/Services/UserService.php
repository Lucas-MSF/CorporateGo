<?php

namespace App\Services;

use App\Http\Requests\User\CreateUserRequest;
use App\Interfaces\Repositories\UserRepositoryInterface;
use App\Interfaces\Services\UserServiceInterface;
use App\Models\User;

class UserService implements UserServiceInterface
{
    public function __construct(private readonly UserRepositoryInterface $userRepository)
    {
    }

    public function create(array $data): User
    {
        return $this->userRepository->create($data);
    }
}
