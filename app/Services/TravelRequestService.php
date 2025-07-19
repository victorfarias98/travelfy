<?php

namespace App\Services;

use App\DTOs\TravelRequestDTO;
use App\Interfaces\TravelRequestRepositoryInterface;
use App\Interfaces\NotificationServiceInterface;
use App\Models\TravelRequest;
use App\Models\User;
use Carbon\Carbon;
use InvalidArgumentException;
use Exception;
use Illuminate\Support\Facades\Auth;

class TravelRequestService
{
    public function __construct(
        private TravelRequestRepositoryInterface $repository,
        private NotificationServiceInterface $notificationService
    ) {}

    public function create(TravelRequestDTO $dto): TravelRequest
    {
        $this->validateTravelDates($dto->departureDate, $dto->returnDate);

        $travelRequest = $this->repository->create([
            'user_id' => $dto->userId,
            'destination' => $dto->destination,
            'departure_date' => $dto->departureDate,
            'return_date' => $dto->returnDate,
            'status' => 'requested'
        ]);

        $this->notificationService->sendNewRequestNotification($travelRequest);

        return $travelRequest;
    }


    public function updateStatus(string $id, string $status, User $user): TravelRequest
    {
        $travelRequest = $this->repository->findById($id);

        if (!$travelRequest) {
            throw new Exception('Pedido de viagem não encontrado');
        }

        if ($travelRequest->user_id === $user->id) {
            throw new Exception('Usuário não pode alterar o status de seu próprio pedido');
        }

        if ($user->role !== 'admin') {
            throw new Exception('Apenas administradores podem alterar o status de pedidos');
        }

        $this->validateStatusTransition($travelRequest->status, $status);

        $updatedTravelRequest = $this->repository->update($id, ['status' => $status]);

        $this->notificationService->sendStatusUpdateNotification($updatedTravelRequest, $status);

        return $updatedTravelRequest;
    }

    public function cancelApprovedRequest(string $id, User $user): TravelRequest
    {
        $travelRequest = $this->repository->findById($id);

        if (!$travelRequest) {
            throw new Exception('Pedido de viagem não encontrado');
        }

        if ($travelRequest->status !== 'approved') {
            throw new Exception('Apenas pedidos aprovados podem ser cancelados por esta função');
        }

        $approvalTime = $travelRequest->updated_at instanceof Carbon ? $travelRequest->updated_at : Carbon::parse($travelRequest->updated_at);
        $hoursElapsed = $approvalTime->diffInHours(now());

        if ($hoursElapsed > 48) {
            throw new Exception('Não é possível cancelar um pedido aprovado após 48 horas');
        }

        $updatedTravelRequest = $this->repository->update($id, ['status' => 'cancelled']);

        $this->notificationService->sendStatusUpdateNotification($updatedTravelRequest, 'cancelled');

        return $updatedTravelRequest;
    }

    public function findById(string $id): ?TravelRequest
    {
        return $this->repository->findById($id);
    }

    public function getAll(array $filters = [], int $perPage = 15)
    {
        return $this->repository->getAll($filters, $perPage);
    }

    private function validateTravelDates(string $departureDate, string $returnDate): void
    {
        $departure = Carbon::parse($departureDate);
        $return = Carbon::parse($returnDate);
        $today = Carbon::today();

        if ($departure->lt($today)) {
            throw new InvalidArgumentException('Data de partida deve ser futura');
        }

        if ($return->lte($departure)) {
            throw new InvalidArgumentException('Data de volta deve ser posterior à data de partida');
        }
    }

    private function validateStatusTransition(string $currentStatus, string $newStatus): void
    {
        $allowedTransitions = [
            'requested' => ['approved', 'cancelled'],
            'approved' => ['cancelled'],
            'cancelled' => []
        ];

        if (!isset($allowedTransitions[$currentStatus]) || 
            !in_array($newStatus, $allowedTransitions[$currentStatus])) {
            throw new InvalidArgumentException(
                "Transição de status inválida: de '{$currentStatus}' para '{$newStatus}'"
            );
        }
    }
}