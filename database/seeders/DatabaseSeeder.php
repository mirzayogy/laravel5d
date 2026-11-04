<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Habit;
use App\Models\Tag;
use App\Models\User;
use App\Services\AchievementService;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use RuntimeException;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database. Demo logins (password is "password"): test@example.com (user), admin@example.com (admin).
     */
    public function run(): void
    {
        // The demo accounts and sample data use a well-known password, so they never go to production.
        if (app()->isProduction()) {
            throw new RuntimeException('DatabaseSeeder creates demo accounts. In production run CategorySeeder, AchievementSeeder and AdminSeeder one by one.');
        }

        // PeriodeSeeder is the course module's example table (periode of terms); the habit tracker does not use it.
        $this->call([CategorySeeder::class, AchievementSeeder::class, PeriodeSeeder::class]);

        mt_srand(42); // the same demo data on every run

        $this->call(AdminSeeder::class);

        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
        $user->profile()->create([
            'bio' => 'Building small habits, one day at a time.',
            'timezone' => 'Asia/Makassar',
            'daily_goal' => 4,
            'onboarded_at' => now(),
        ]);

        $tags = collect(['morning', 'evening', 'quick', 'outdoor'])
            ->map(fn (string $name) => Tag::firstOrCreate(['name' => $name]));

        $today = $user->today();

        // About 7 months of history. Some whole days are skipped so streaks end, and older months
        // are quieter, which gives the year view some shape.
        $history = 210;
        $activeDay = [];
        foreach (range(1, $history) as $daysAgo) {
            $activeDay[$daysAgo] = $daysAgo <= 9 || mt_rand(1, 100) <= 45 + (int) (45 * (1 - $daysAgo / $history));
        }
        $categories = Category::all()->keyBy('slug');

        foreach (config('habitude.starters') as $slug => $starters) {
            foreach ($starters as [$name, $target, $unit]) {

                /** @var Habit $habit */
                $habit = $user->habits()->create([
                    'category_id' => $categories[$slug]->id,
                    'name' => $name,
                    'description' => $categories[$slug]->name.' starter habit.',
                    'target_count' => $target,
                    'unit' => $unit,
                    'weekly_goal' => $name === 'Workout' ? 4 : null, // one weekly habit to show "4 days a week"
                ]);

                $habit->forceFill(['created_at' => $today->subDays($history + 1)])->save();
                $habit->tags()->attach($tags->random(2)->pluck('id'));

                // Every habit gets a reminder; the first three also get a second one.
                $habit->reminders()->create(['remind_at' => sprintf('%02d:00', mt_rand(6, 21)), 'days_of_week' => [1, 2, 3, 4, 5, 6, 7]]);

                // The first three habits keep a 9-day streak up to yesterday, today is left open.
                $streaky = $habit->id <= 3;
                foreach (range(1, $history) as $daysAgo) {
                    if (! $activeDay[$daysAgo]) {
                        continue;
                    }

                    $roll = mt_rand(1, 100);
                    $value = $streaky && $daysAgo <= 9 ? $target
                        : ($roll <= 70 ? $target : ($roll <= 85 ? max(1, intdiv($target, 2)) : 0));

                    if ($value > 0) {
                        $habit->logs()->create(['logged_date' => $today->subDays($daysAgo)->toDateString(), 'value' => $value]);
                    }
                }
            }
        }

        // A rest day on the most recent day with no check-in, so the demo shows a streak bridged by one.
        $missed = collect(range(10, $history))->first(fn ($d) => ! $activeDay[$d]);
        if ($missed) {
            $user->restDays()->create(['rest_date' => $today->subDays($missed)->toDateString()]);
        }

        foreach (range(0, 24) as $daysAgo) {
            $date = $today->subDays($daysAgo)->toDateString();
            $mood = $user->moodEntries()->create([
                'entry_date' => $date,
                'mood_level' => mt_rand(2, 5),
                'note' => $daysAgo % 4 === 0 ? 'A calm, productive day.' : null,
            ]);

            if ($daysAgo % 3 === 0) {
                $user->journalEntries()->create([
                    'mood_entry_id' => $mood->id,
                    'entry_date' => $date,
                    'title' => 'Notes from '.$today->subDays($daysAgo)->format('j M'),
                    'content' => "Kept my habits going today.\nSmall steps add up, and the streak feels good.",
                ]);
            }
        }

        app(AchievementService::class)->award($user);
    }
}
