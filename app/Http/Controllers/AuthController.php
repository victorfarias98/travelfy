<?php

namespace App\Http\Controllers;

use App\DTOs\AuthDTO;
use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\RegisterUserRequest;
use App\Interfaces\AuthServiceInterface;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    public function __construct(
        protected AuthServiceInterface $authService
    ) {}

    public function login(LoginUserRequest $request): JsonResponse
    {
        try {
            $token = $this->authService->login(AuthDTO::fromRequest($request->validated()));

            return response()->json(['token' => $token], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    public function register(RegisterUserRequest $request): JsonResponse
    {
        try {
            $user = $this->authService->register($request->validated());
            return response()->json([
                'message' => 'Usuário registrado com sucesso!',
                'user' => $user
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    public function logout(): JsonResponse
    {
        try {
            $this->authService->logout();
            return response()->json(['message' => 'Logout realizado com sucesso.'], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    public function refresh(): JsonResponse
    {
        try {
            return response()->json(['token' => $this->authService->refresh()], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    public function me(): JsonResponse
    {
        try {
            return response()->json(['user' => $this->authService->me()], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }

}
