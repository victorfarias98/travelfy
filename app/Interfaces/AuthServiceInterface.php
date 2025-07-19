<?php

namespace App\Interfaces;

use App\DTOs\AuthDTO;

interface AuthServiceInterface
{
    public function login(AuthDTO $authDTO): string;
    public function logout(): void;

    public function register(array $data);
}
