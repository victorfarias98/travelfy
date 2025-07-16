<?php

namespace App\Services;

use App\Repositories\AuthRepository;
use App\Models\User;
use App\Repositories\Contracts\AuthRepositoryInterface;

class AuthService
{
    public function __construct(protected AuthRepositoryInterface $authRepository) {
    }

    public function login(array $credentials)
    {
        return $this->authRepository->attempt($credentials);
    }

    public function me(): User
    {
        return $this->authRepository->user();
    }

    public function logout(): void
    {
        $this->authRepository->logout();
    }

    public function refresh(): string
    {
        return $this->authRepository->refresh();
    }

    public function getTTL(): mixed
    {
        return $this->authRepository->getTTL();
    }
} 