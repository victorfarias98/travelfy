<?php

namespace App\Services\Contracts;

use App\Models\User;

interface AuthServiceInterface
{
    public function login(array $credentials);
    public function me(): User;
    public function logout(): void;
    public function refresh(): string;
    public function getTTL(): mixed;
} 