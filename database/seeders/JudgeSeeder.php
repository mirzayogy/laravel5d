<?php

namespace Database\Seeders;

use App\Models\Judge;
use App\Models\Species;
use Illuminate\Database\Seeder;

class JudgeSeeder extends Seeder
{
    /**
     * Seed judges and link each one to the species they are certified for.
     */
    public function run(): void
    {
        $species = Species::all();

        Judge::factory()
            ->count(6)
            ->create()
            ->each(function (Judge $judge) use ($species) {
                $judge->species()->attach(
                    $species->random(random_int(2, 4))->pluck('id')->all()
                );
            });
    }
}
