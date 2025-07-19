<?php

namespace App\Repositories;

use App\Models\User;
use App\Interfaces\AuthRepositoryInterface;
use Exception;
use Illuminate\Database\QueryException;
use Throwable;

class AuthRepository implements AuthRepositoryInterface
{
    public function findByEmail(string $email): ?User
    {
        try {
            return User::query()->where('email', $email)->first();
        } catch (QueryException $e) {
            throw new Exception('Erro ao recuperar usuário: ' . $e->getMessage());
        } catch (Exception $e) {
            throw new Exception('Erro inesperado: ' . $e->getMessage());
        }
    }

    public function createUser(array $data): User
    {
        try {
            return User::query()->create($data);
        } catch (QueryException $e) {
            throw new Exception('Erro ao criar usuário: ' . $e->getMessage());
        } catch (Throwable $e) {
            throw new Exception('Erro inesperado: ' . $e->getMessage());
        }
    }
}
