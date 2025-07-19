<?php

namespace App\Services;

use App\DTOs\AuthDTO;
use App\Models\User;
use App\Interfaces\AuthRepositoryInterface;
use App\Interfaces\AuthServiceInterface;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

readonly class AuthService implements AuthServiceInterface
{
    public function __construct(
        protected AuthRepositoryInterface $authRepository
    ) {}

    /**
     * @throws AuthenticationException
     */
    public function login(AuthDTO $authDTO): string
    {
        $user = $this->authRepository->findByEmail($authDTO->email);

        if (!$user || !Hash::check($authDTO->password, $user->password)) {
            throw new AuthenticationException('Credenciais inválidas.');
        }
        Auth::login($user);

        return Auth::tokenById($user->id);
    }

    public function logout(): void
    {
       Auth::invalidate();
       Auth::logout();
    }

    public function refreshToken(): string
    {
        return Auth::refresh(true,true);
    }

    public function register(array $data): User
    {
        return $this->authRepository->createUser($data);
    }

    public function me(): ?Authenticatable
    {
        return Auth::user();
    }
}
