<?php

namespace Database\Seeders;

use App\Models\Player;
use App\Models\Team;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PlayerSeeder extends Seeder
{
    public function run(): void
    {
        Team::all()->each(function (Team $team) {
            Player::factory(7)
                ->sequence(fn ($seq) => ['jersey_number' => $seq->index + 1])
                ->create(['team_id' => $team->id]);
        });
    }
}