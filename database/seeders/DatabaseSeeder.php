<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
        User::factory(5)->create();

        $this->call([
            ProfileSeeder::class,
            VenueSeeder::class,
            FieldSeeder::class,
            FacilitySeeder::class,
            TeamSeeder::class,
            PlayerSeeder::class,
            TournamentSeeder::class,
            BookingSeeder::class,
        ]);
    }
}