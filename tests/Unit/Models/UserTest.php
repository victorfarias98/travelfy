<?php

namespace Tests\Unit\Models;

use App\Models\User;
use App\Models\TravelRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_has_many_travel_requests()
    {
        $user = User::factory()->create();
        TravelRequest::factory()->count(2)->create(['user_id' => $user->id]);
        $this->assertCount(2, $user->travelRequests);
    }

    public function test_fillable_attributes()
    {
        $user = new User([
            'name' => 'Fulano',
            'email' => 'fulano@example.com',
        ]);
        $this->assertEquals('Fulano', $user->name);
        $this->assertEquals('fulano@example.com', $user->email);
    }

    public function test_hidden_attributes()
    {
        $user = User::factory()->create();
        $array = $user->toArray();
        $this->assertArrayNotHasKey('password', $array);
    }
} 