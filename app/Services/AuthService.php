<?php

namespace App\Services;

use App\Exceptions\Auth\IncorrectCredentialsException;
use App\Interfaces\Services\AuthServiceInterface;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthService implements AuthServiceInterface
{
    public function login(string $email, string $password): array
    {
        $this->validateLogin($email, $password);
        $user = auth()->user();
        return [
            'name'         => $user->name,
            'access_token' => JWTAuth::fromUser($user),
        ];
    }

    private function validateLogin(string $email, string $password): void
    {
        if (!auth()->attempt([
            'email' => $email,
            'password' => $password,
        ])) {
            throw new IncorrectCredentialsException();
        }
    }

    public function logout(): void
    {
        auth()->logout();
    }
}
