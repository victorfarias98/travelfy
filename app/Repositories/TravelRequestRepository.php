<?php

namespace App\Repositories;

use App\Interfaces\TravelRequestRepositoryInterface;
use App\Models\TravelRequest;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class TravelRequestRepository implements TravelRequestRepositoryInterface
{
    public function create(array $data): TravelRequest
    {
        return TravelRequest::create($data);
    }

    public function update(string $id, array $data): TravelRequest
    {
        $travelRequest = TravelRequest::findOrFail($id);
        $travelRequest->update($data);
        return $travelRequest->fresh();
    }

    public function findById(string $id): ?TravelRequest
    {
        return TravelRequest::with('user')->find($id);
    }

    public function getAll(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = TravelRequest::with('user');

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['destination'])) {
            $query->where('destination', 'LIKE', '%' . $filters['destination'] . '%');
        }

        if (!empty($filters['departure_date_from'])) {
            $query->where('departure_date', '>=', $filters['departure_date_from']);
        }

        if (!empty($filters['departure_date_to'])) {
            $query->where('departure_date', '<=', $filters['departure_date_to']);
        }

        if (!empty($filters['return_date_from'])) {
            $query->where('return_date', '>=', $filters['return_date_from']);
        }

        if (!empty($filters['return_date_to'])) {
            $query->where('return_date', '<=', $filters['return_date_to']);
        }

        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (!empty($filters['created_from'])) {
            $query->where('created_at', '>=', $filters['created_from']);
        }

        if (!empty($filters['created_to'])) {
            $query->where('created_at', '<=', $filters['created_to']);
        }

        $orderBy = $filters['order_by'] ?? 'created_at';
        $orderDirection = $filters['order_direction'] ?? 'desc';
        $query->orderBy($orderBy, $orderDirection);

        return $query->paginate($perPage);
    }

    public function delete(string $id): bool
    {
        $travelRequest = TravelRequest::find($id);
        return $travelRequest ? $travelRequest->delete() : false;
    }

    public function getByStatus(string $status): Collection
    {
        return TravelRequest::with('user')
            ->where('status', $status)
            ->get();
    }

    public function getByUser(string $userId, array $filters = []): Collection
    {
        $query = TravelRequest::where('user_id', $userId);

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    public function getUpcomingTravels(int $days = 7): Collection
    {
        return TravelRequest::with('user')
            ->where('status', 'approved')
            ->whereBetween('departure_date', [now(), now()->addDays($days)])
            ->orderBy('departure_date')
            ->get();
    }
}