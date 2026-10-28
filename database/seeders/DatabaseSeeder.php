<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Challenge;
use App\Models\Competition;
use App\Models\Hint;
use App\Models\Profile;
use App\Models\Submission;
use App\Models\Tag;
use App\Models\Team;
use App\Models\User;
use App\Models\Writeup;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(PeriodeSeeder::class);
        $this->call(CategorySeeder::class);

        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'role' => 'admin',
        ]);

        $tags = collect(['beginner', 'sql-injection', 'xss', 'rsa', 'steganography', 'buffer-overflow'])
            ->map(fn (string $name) => Tag::create(['name' => $name]));

        $competition = Competition::factory()->create([
            'title' => 'Flagboard Demo CTF',
            'slug' => 'flagboard-demo-ctf',
        ]);

        $teams = Team::factory(3)->create();

        foreach ($teams as $index => $team) {
            $players = User::factory(2)->create(['team_id' => $team->id]);

            if ($index === 0) {
                $players->first()->update(['name' => 'Player One', 'email' => 'player@example.com']);
            }

            $players->each(fn (User $user) => Profile::factory()->create(['user_id' => $user->id]));

            $competition->teams()->attach($team->id, ['registered_at' => now()]);
        }

        $challenges = Category::all()->map(function (Category $category) use ($competition, $tags) {
            $challenge = Challenge::factory()->create([
                'competition_id' => $competition->id,
                'category_id' => $category->id,
                'flag_hash' => Hash::make('FLAG{demo_'.$category->id.'}'),
            ]);

            $challenge->tags()->attach($tags->random(2)->pluck('id'));

            Hint::factory(2)->create(['challenge_id' => $challenge->id]);

            return $challenge;
        });

        foreach ($teams as $team) {
            $member = $team->users()->first();

            $solved = $challenges->random(3);

            foreach ($solved as $challenge) {
                Submission::factory()->create([
                    'team_id' => $team->id,
                    'challenge_id' => $challenge->id,
                    'user_id' => $member->id,
                    'is_correct' => true,
                ]);
            }

            Submission::factory()->create([
                'team_id' => $team->id,
                'challenge_id' => $challenges->diff($solved)->first()->id,
                'user_id' => $member->id,
                'is_correct' => false,
            ]);

            $team->hints()->attach(
                $challenges->first()->hints()->first()->id,
                ['unlocked_at' => now()]
            );
        }

        User::where('role', 'player')->take(3)->get()->each(
            fn (User $user) => Writeup::factory()->create([
                'user_id' => $user->id,
                'challenge_id' => $challenges->first()->id,
            ])
        );
    }
}
