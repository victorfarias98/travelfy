<?php

namespace Database\Seeders;

use App\Models\TravelRequest;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin'
        ]);

        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
            'role' => 'user'
        ]);

        $anotherUser = User::factory()->create([
            'name' => 'Another User',
            'email' => 'another-user@example.com',
            'password' => Hash::make('password'),
            'role' => 'user'
        ]);

        TravelRequest::factory()->create([
            'user_id' => $anotherUser->id,
            'destination' => 'CNF',
            'departure_date' => '2025-07-19',
            'return_date' => '2025-07-20',
            'status' => 'requested',
        ]
        );
        TravelRequest::factory()->create([
            'user_id' => $user->id,
            'destination' => 'CNF',
            'departure_date' => '2025-07-10',
            'return_date' => '2025-07-20',
            'status' => 'cancelled',
        ]
        );
        TravelRequest::factory()->create([
            'user_id' => $user->id,
            'destination' => 'CNF',
            'departure_date' => '2025-07-09',
            'return_date' => '2025-07-20',
            'status' => 'approved',
        ]
        );
    }
}
