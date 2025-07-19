<?php

namespace Tests\Unit\Services;

use App\DTOs\AuthDTO;
use App\Interfaces\AuthRepositoryInterface;
use App\Models\User;
use App\Services\AuthService;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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
        // Arrange
        $email = 'nonexistent@example.com';
        $password = 'password123';
        
        $authDTO = new AuthDTO($email, $password);
        
        $this->authRepository->shouldReceive('findByEmail')
            ->once()
            ->with($email)
            ->andReturn(null);

        // Assert
        $this->expectException(AuthenticationException::class);
        $this->expectExceptionMessage('Credenciais inválidas.');

        // Act
        $this->service->login($authDTO);
    }
 
    /** @test */
    public function refresh_token_should_return_new_token()
    {
        // Arrange
        $expectedToken = 'new-jwt-token-456';
        
        Auth::shouldReceive('refresh')
            ->once()
            ->with(true, true)
            ->andReturn($expectedToken);

        // Act
        $result = $this->service->refreshToken();

        // Assert
        $this->assertEquals($expectedToken, $result);
    }

    /** @test */
    public function me_should_return_authenticated_user()
    {
        // Arrange
        $user = Mockery::mock(Authenticatable::class);
        
        Auth::shouldReceive('user')
            ->once()
            ->andReturn($user);

        // Act
        $result = $this->service->me();

        // Assert
        $this->assertSame($user, $result);
    }

    /** @test */
    public function me_should_return_null_when_not_authenticated()
    {
        // Arrange
        Auth::shouldReceive('user')
            ->once()
            ->andReturn(null);

        // Act
        $result = $this->service->me();

        // Assert
        $this->assertNull($result);
    }
}