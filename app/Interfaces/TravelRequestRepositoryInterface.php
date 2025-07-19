<?php

namespace App\Interfaces;

use App\Models\TravelRequest;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface TravelRequestRepositoryInterface
{
    public function create(array $data): TravelRequest;
    
    public function update(string $id, array $data): TravelRequest;
    
    public function findById(string $id): ?TravelRequest;
    
    public function getAll(array $filters = [], int $perPage = 15): LengthAwarePaginator;
    
    public function delete(string $id): bool;
    
    public function getByStatus(string $status): Collection;
    
    public function getByUser(string $userId, array $filters = []): Collection;
    
    public function getUpcomingTravels(int $days = 7): Collection;
}