<?php

namespace Tests\Unit\Models;

use App\Models\TravelRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TravelRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_belongs_to_user()
    {
        $user = User::factory()->create();
        $travelRequest = TravelRequest::factory()->create(['user_id' => $user->id]);
        $this->assertInstanceOf(User::class, $travelRequest->user);
        $this->assertEquals($user->id, $travelRequest->user->id);
    }

    public function test_casts_dates()
    {
        $travelRequest = TravelRequest::factory()->create([
            'departure_date' => '2024-12-10',
            'return_date' => '2024-12-15',
        ]);
        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $travelRequest->departure_date);
        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $travelRequest->return_date);
    }

    public function test_fillable_attributes()
    {
        $travelRequest = new TravelRequest([
            'destination' => 'Recife',
            'status' => 'pending',
        ]);
        $this->assertEquals('Recife', $travelRequest->destination);
        $this->assertEquals('pending', $travelRequest->status);
    }
} 