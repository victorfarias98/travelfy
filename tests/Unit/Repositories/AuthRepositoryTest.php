<?php

namespace Tests\Unit\Repositories;

use App\Models\User;
use App\Repositories\AuthRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new AuthRepository();
    }

    public function test_create_user()
    {
        $data = [
            'name' => 'Teste',
            'email' => 'teste@example.com',
            'password' => bcrypt('senha123'),
        ];
        $user = $this->repository->createUser($data);
        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('teste@example.com', $user->email);
    }

    public function test_find_user_by_email()
    {
        $user = User::factory()->create(['email' => 'findme@example.com']);
        $found = $this->repository->findByEmail('findme@example.com');
        $this->assertEquals($user->id, $found->id);
    }
} 