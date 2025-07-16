<?php

namespace App\Repositories\Contracts;

use App\Models\User;

interface AuthRepositoryInterface
{
    public function attempt(array $credentials): bool;
    public function user(): User;
    public function logout(): void;
    public function refresh(): mixed;
    public function getTTL(): mixed;
} 