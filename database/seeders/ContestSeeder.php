<?php

namespace Database\Seeders;

use App\Models\Bird;
use App\Models\Contest;
use App\Models\Judge;
use App\Models\Organizer;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

class ContestSeeder extends Seeder
{
    /**
     * Seed organizers and contests, then register birds in each contest.
     */
    public function run(): void
    {
        $organizers = collect([
            ['Perkumpulan Kicau Mania Banjarbaru', 'Banjarbaru'],
            ['Gantangan Borneo Palangka Raya', 'Palangka Raya'],
            ['Persatuan Pecinta Burung Samarinda', 'Samarinda'],
            ['Komunitas Burung Berkicau Pontianak', 'Pontianak'],
        ])->map(fn (array $organizer) => Organizer::factory()->create([
            'name' => $organizer[0],
            'city' => $organizer[1],
        ]));
        $judges = Judge::all();
        $birds = Bird::all();

        foreach (['finished', 'finished', 'finished', 'ongoing', 'upcoming', 'upcoming'] as $state) {
            $contest = Contest::factory()
                ->{$state}()
                ->create([
                    'organizer_id' => $organizers->random()->id,
                    'chief_judge_id' => $judges->random()->id,
                ]);

            $this->registerEntrants($contest, $birds);
        }
    }

    /**
     * Register up to ten birds in a contest. Only finished contests have scores and ranks.
     *
     * @param  Collection<int, Bird>  $birds
     */
    private function registerEntrants(Contest $contest, Collection $birds): void
    {
        $entrants = $birds->random(min(10, $birds->count()))->values();
        $finished = $contest->status === 'finished';

        $entries = [];

        foreach ($entrants as $index => $bird) {
            $entries[$bird->id] = [
                'entry_number' => $index + 1,
                'division' => fake()->randomElement(['novice', 'intermediate', 'champion']),
                'total_score' => $finished ? fake()->randomFloat(2, 68, 98) : null,
                'final_rank' => null,
                'registered_at' => $finished
                    ? $contest->held_on->copy()->subDays(random_int(3, 21))
                    : now()->subDays(random_int(0, 10)),
            ];
        }

        if ($finished) {
            $rank = 1;

            foreach (collect($entries)->sortByDesc('total_score')->keys() as $birdId) {
                $entries[$birdId]['final_rank'] = $rank++;
            }
        }

        $contest->birds()->attach($entries);
    }
}
