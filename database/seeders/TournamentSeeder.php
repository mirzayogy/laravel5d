<?php

namespace Database\Seeders;

use App\Models\Team;
use App\Models\Tournament;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TournamentSeeder extends Seeder
{
    public function run(): void
    {
        Tournament::factory(2)->create()->each(function (Tournament $tournament) {
            Team::inRandomOrder()->take(4)->get()->each(function (Team $team) use ($tournament) {
                $tournament->teams()->attach($team->id, [
                    'registered_at' => now(),
                    'status' => 'approved',
                ]);
            });
        });
    }
}