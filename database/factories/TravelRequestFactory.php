<?php

namespace Database\Factories;

use App\Models\TravelRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TravelRequestFactory extends Factory
{
    public function definition(): array
    {
        $departureDate = $this->faker->dateTimeBetween('+1 week', '+3 months');
        $returnDate = $this->faker->dateTimeBetween($departureDate, $departureDate->format('Y-m-d') . ' +2 weeks');

        return [
            'user_id' => User::factory(),
            'destination' => $this->faker->city() . ', ' . $this->faker->country(),
            'departure_date' => $departureDate,
            'return_date' => $returnDate,
            'status' => $this->faker->randomElement(['requested', 'approved', 'cancelled']),
        ];
    }

    public function requested(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'requested',
        ]);
    }

    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'approved',
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'cancelled',
        ]);
    }
}