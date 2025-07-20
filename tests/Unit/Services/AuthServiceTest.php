<?php

namespace Tests\Unit\Services;

use App\DTOs\AuthDTO;
use App\Interfaces\AuthRepositoryInterface;
use App\Services\AuthService;
use Illuminate\Auth\AuthenticationException;
use PHPUnit\Framework\TestCase;
use Mockery;
use Mockery\MockInterface;

class AuthServiceTest extends TestCase
{
    private AuthService $service;
    private MockInterface $authRepository;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->authRepository = Mockery::mock(AuthRepositoryInterface::class);
        $this->service = new AuthService($this->authRepository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /** @test */
    public function login_should_throw_exception_when_user_not_found()
    {
        $email = 'nonexistent@example.com';
        $password = 'password123';
        
        $authDTO = new AuthDTO($email, $password);
        
        $this->authRepository->shouldReceive('findByEmail')
            ->once()
            ->with($email)
            ->andReturn(null);

        $this->expectException(AuthenticationException::class);
        $this->expectExceptionMessage('Credenciais inválidas.');

        $this->service->login($authDTO);
    }
}