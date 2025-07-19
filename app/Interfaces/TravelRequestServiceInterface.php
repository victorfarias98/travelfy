<?php

namespace App\Interfaces;

use App\DTOs\TravelRequestDTO;
use App\Models\TravelRequest;
use App\Models\User;

interface TravelRequestServiceInterface
{
    public function create(TravelRequestDTO $dto): TravelRequest;

    public function updateStatus(string $id, string $status, User $user): TravelRequest;

    public function cancelApprovedRequest(string $id, User $user): TravelRequest;

    public function findById(string $id): ?TravelRequest;

    public function getAll(array $filters = [], int $perPage = 15);
}