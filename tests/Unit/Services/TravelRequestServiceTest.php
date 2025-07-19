<?php

namespace Tests\Unit\Services;

use App\DTOs\TravelRequestDTO;
use App\Interfaces\NotificationServiceInterface;
use App\Interfaces\TravelRequestRepositoryInterface;
use App\Models\TravelRequest;
use App\Models\User;
use App\Services\TravelRequestService;
use Carbon\Carbon;
use Exception;
use InvalidArgumentException;
use Illuminate\Support\Facades\Auth;
use PHPUnit\Framework\TestCase;
use Mockery;
use Mockery\MockInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class TravelRequestServiceTest extends TestCase
{
    private TravelRequestService $service;
    private MockInterface $repository;
    private MockInterface $notificationService;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->repository = Mockery::mock(TravelRequestRepositoryInterface::class);
        $this->notificationService = Mockery::mock(NotificationServiceInterface::class);
        
        $this->service = new TravelRequestService(
            $this->repository,
            $this->notificationService
        );
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /** @test */
    public function create_should_throw_exception_when_departure_date_is_in_past()
    {
        // Arrange
        Carbon::setTestNow('2024-01-15');
        
        $dto = new TravelRequestDTO(
            userId: '1',
            destination: 'Paris',
            departureDate: '2024-01-10', // Data no passado
            returnDate: '2024-01-20'
        );

        // Assert
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Data de partida deve ser futura');

        // Act
        $this->service->create($dto);
    }

    /** @test */
    public function create_should_throw_exception_when_return_date_is_before_departure()
    {
        // Arrange
        Carbon::setTestNow('2024-01-01');
        
        $dto = new TravelRequestDTO(
            userId: '1',
            destination: 'Paris',
            departureDate: '2024-01-20',
            returnDate: '2024-01-15' // Data de volta anterior à partida
        );

        // Assert
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Data de volta deve ser posterior à data de partida');

        // Act
        $this->service->create($dto);
    }

    /** @test */
    public function update_status_should_update_status_successfully()
    {
        // Arrange
        $travelRequestId = '123';
        $newStatus = 'approved';
        $admin = new User(['id' => 2, 'role' => 'admin']);
        
        $travelRequest = new TravelRequest([
            'id' => $travelRequestId,
            'user_id' => 1,
            'status' => 'requested'
        ]);
        
        $updatedTravelRequest = new TravelRequest([
            'id' => $travelRequestId,
            'user_id' => 1,
            'status' => $newStatus
        ]);
        
        $this->repository->shouldReceive('findById')
            ->once()
            ->with($travelRequestId)
            ->andReturn($travelRequest);
            
        $this->repository->shouldReceive('update')
            ->once()
            ->with($travelRequestId, ['status' => $newStatus])
            ->andReturn($updatedTravelRequest);
            
        $this->notificationService->shouldReceive('sendStatusUpdateNotification')
            ->once()
            ->with($updatedTravelRequest, $newStatus);

        // Act
        $result = $this->service->updateStatus($travelRequestId, $newStatus, $admin);

        // Assert
        $this->assertSame($updatedTravelRequest, $result);
    }


    /** @test */
    public function update_status_should_throw_exception_when_user_is_not_admin()
    {
        // Arrange
        $travelRequestId = '123';
        $user = new User(['id' => 2, 'role' => 'user']);
        
        $travelRequest = new TravelRequest([
            'id' => $travelRequestId,
            'user_id' => 1,
            'status' => 'requested'
        ]);
        
        $this->repository->shouldReceive('findById')
            ->once()
            ->with($travelRequestId)
            ->andReturn($travelRequest);

        // Assert
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Apenas administradores podem alterar o status de pedidos');

        // Act
        $this->service->updateStatus($travelRequestId, 'approved', $user);
    }

    /** @test */
    public function update_status_should_throw_exception_for_invalid_status_transition()
    {
        // Arrange
        $travelRequestId = '123';
        $admin = new User(['id' => 2, 'role' => 'admin']);
        
        $travelRequest = new TravelRequest([
            'id' => $travelRequestId,
            'user_id' => 1,
            'status' => 'cancelled' // Status que não permite transições
        ]);
        
        $this->repository->shouldReceive('findById')
            ->once()
            ->with($travelRequestId)
            ->andReturn($travelRequest);

        // Assert
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Transição de status inválida: de 'cancelled' para 'approved'");

        // Act
        $this->service->updateStatus($travelRequestId, 'approved', $admin);
    }

    /** @test */
    public function cancel_approved_request_should_throw_exception_when_not_found()
    {
        // Arrange
        $travelRequestId = '123';
        $user = new User(['id' => 1]);
        
        $this->repository->shouldReceive('findById')
            ->once()
            ->with($travelRequestId)
            ->andReturn(null);

        // Assert
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Pedido de viagem não encontrado');

        // Act
        $this->service->cancelApprovedRequest($travelRequestId, $user);
    }

    /** @test */
    public function cancel_approved_request_should_throw_exception_when_not_approved()
    {
        // Arrange
        $travelRequestId = '123';
        $user = new User(['id' => 1]);
        
        $travelRequest = new TravelRequest([
            'id' => $travelRequestId,
            'status' => 'requested' // Não aprovado
        ]);
        
        $this->repository->shouldReceive('findById')
            ->once()
            ->with($travelRequestId)
            ->andReturn($travelRequest);

        // Assert
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Apenas pedidos aprovados podem ser cancelados por esta função');

        // Act
        $this->service->cancelApprovedRequest($travelRequestId, $user);
    }

    /** @test */
    public function find_by_id_should_return_travel_request()
    {
        // Arrange
        $travelRequestId = '123';
        $travelRequest = new TravelRequest(['id' => $travelRequestId]);
        
        $this->repository->shouldReceive('findById')
            ->once()
            ->with($travelRequestId)
            ->andReturn($travelRequest);

        // Act
        $result = $this->service->findById($travelRequestId);

        // Assert
        $this->assertSame($travelRequest, $result);
    }

    /** @test */
    public function find_by_id_should_return_null_when_not_found()
    {
        // Arrange
        $travelRequestId = '123';
        
        $this->repository->shouldReceive('findById')
            ->once()
            ->with($travelRequestId)
            ->andReturn(null);

        // Act
        $result = $this->service->findById($travelRequestId);

        // Assert
        $this->assertNull($result);
    }

    /** @test */
    public function get_all_should_return_paginated_results()
    {
        // Arrange
        $filters = ['status' => 'approved'];
        $perPage = 10;
        $expectedResults = Mockery::mock(LengthAwarePaginator::class);
        
        $this->repository->shouldReceive('getAll')
            ->once()
            ->with($filters, $perPage)
            ->andReturn($expectedResults);

        // Act
        $result = $this->service->getAll($filters, $perPage);

        // Assert
        $this->assertSame($expectedResults, $result);
    }

    /** @test */
    public function get_all_should_use_default_parameters()
    {
        // Arrange
        $expectedResults = Mockery::mock(LengthAwarePaginator::class);
        
        $this->repository->shouldReceive('getAll')
            ->once()
            ->with([], 15) // Valores padrão
            ->andReturn($expectedResults);

        // Act
        $result = $this->service->getAll();

        // Assert
        $this->assertSame($expectedResults, $result);
    }
}