<?php

namespace App\DTOs;

class TravelRequestDTO
{
    public function __construct(
        public readonly string $userId,
        public readonly string $destination,
        public readonly string $departureDate,
        public readonly string $returnDate,
        public readonly ?string $id = null,
        public readonly ?string $status = null
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            userId: $data['user_id'],
            destination: $data['destination'],
            departureDate: $data['departure_date'],
            returnDate: $data['return_date'],
            id: $data['id'] ?? null,
            status: $data['status'] ?? null
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->userId,
            'destination' => $this->destination,
            'departure_date' => $this->departureDate,
            'return_date' => $this->returnDate,
            'status' => $this->status
        ];
    }
}