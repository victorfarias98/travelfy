<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Contracts\AuthRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class AuthRepository implements AuthRepositoryInterface
{
    public function attempt(array $credentials): bool
    {
        return auth('api')->attempt($credentials);
    }

    public function user(): User
    {
        return Auth::guard('api')->user();
    }

    public function logout(): void
    {
       auth('api')->logout();
    }

    public function refresh(): mixed
    {        
        return auth('api')->refresh();
    }

    public function getTTL(): mixed
    {
        return auth('api')->factory()->getTTL();
    }
} 