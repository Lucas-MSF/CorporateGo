<?php

namespace App\Interfaces\Services;

use App\Models\User;

interface UserServiceInterface
{
    public function create(array $data): User;
}
