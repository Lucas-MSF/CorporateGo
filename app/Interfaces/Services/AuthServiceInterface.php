<?php

namespace App\Interfaces\Services;

interface AuthServiceInterface
{
    public function login(string $email, string $password): array;


    public function logout(): void;
}
