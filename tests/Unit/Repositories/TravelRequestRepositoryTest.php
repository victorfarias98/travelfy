<?php

namespace Tests\Unit\Repositories;

use App\Models\TravelRequest;
use App\Repositories\TravelRequestRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TravelRequestRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new TravelRequestRepository();
    }

    public function test_create_travel_request()
    {
        $user = \App\Models\User::factory()->create();
        $data = [
            'user_id' => $user->id,
            'destination' => 'Salvador',
            'departure_date' => '2024-12-10',
            'return_date' => '2024-12-15',
            'status' => 'requested',
        ];
        $travelRequest = $this->repository->create($data);
        $this->assertInstanceOf(TravelRequest::class, $travelRequest);
        $this->assertEquals('Salvador', $travelRequest->destination);
    }

    public function test_find_travel_request_by_id()
    {
        $travelRequest = TravelRequest::factory()->create();
        $found = $this->repository->findById($travelRequest->id);
        $this->assertEquals($travelRequest->id, $found->id);
    }

    public function test_update_travel_request()
    {
        $travelRequest = TravelRequest::factory()->create(['status' => 'requested']);
        $updated = $this->repository->update($travelRequest->id, ['status' => 'approved']);
        $this->assertEquals('approved', $updated->status);
    }

    public function test_delete_travel_request()
    {
        $travelRequest = TravelRequest::factory()->create();
        $result = $this->repository->delete($travelRequest->id);
        $this->assertTrue($result);
        $this->assertNull(TravelRequest::find($travelRequest->id));
    }

    public function test_filter_travel_requests_by_status()
    {
        TravelRequest::factory()->count(2)->create(['status' => 'requested']);
        TravelRequest::factory()->count(3)->create(['status' => 'approved']);
        $requested = $this->repository->getByStatus('requested');
        $this->assertCount(2, $requested);
    }
} 