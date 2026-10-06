<?php

namespace Database\Seeders;

use App\Models\Bird;
use App\Models\HealthRecord;
use App\Models\Profile;
use App\Models\Species;
use App\Models\TrainingLog;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

class OwnerSeeder extends Seeder
{
    /**
     * Seed bird owners with their profiles, birds, health records and training logs.
     *
     * The demo account (test@example.com) always gets fixed amounts so that
     * relationship checks are repeatable: 6 birds, 3 health records and
     * 4 training logs per bird.
     */
    public function run(): void
    {
        $species = Species::all();

        $demo = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $this->seedOwner($demo, 6, $species, fixed: true);

        $owners = User::factory()->count(5)->create();

        $owners->each(fn (User $owner) => $this->seedOwner($owner, random_int(3, 5), $species));

        $this->seedPedigree($owners->first(), $species);
    }

    /**
     * Create a profile and a set of birds with their records for one owner.
     *
     * @param  Collection<int, Species>  $species
     */
    private function seedOwner(User $user, int $birdCount, Collection $species, bool $fixed = false): void
    {
        Profile::factory()->for($user)->create();

        $birds = Bird::factory()
            ->count($birdCount)
            ->for($user, 'owner')
            ->state(fn () => ['species_id' => $species->random()->id])
            ->create();

        foreach ($birds as $bird) {
            HealthRecord::factory()
                ->count($fixed ? 3 : random_int(1, 3))
                ->for($bird)
                ->create();

            TrainingLog::factory()
                ->count($fixed ? 4 : random_int(2, 5))
                ->for($bird)
                ->create();
        }
    }

    /**
     * Create three breeding pairs, each with two offspring, to fill the sire and dam links.
     *
     * @param  Collection<int, Species>  $species
     */
    private function seedPedigree(User $owner, Collection $species): void
    {
        foreach ($species->random(3) as $pairSpecies) {
            $sire = Bird::factory()->male()->for($owner, 'owner')->create(['species_id' => $pairSpecies->id]);
            $dam = Bird::factory()->female()->for($owner, 'owner')->create(['species_id' => $pairSpecies->id]);

            Bird::factory()
                ->count(2)
                ->for($owner, 'owner')
                ->create([
                    'species_id' => $pairSpecies->id,
                    'sire_id' => $sire->id,
                    'dam_id' => $dam->id,
                ]);
        }
    }
}
