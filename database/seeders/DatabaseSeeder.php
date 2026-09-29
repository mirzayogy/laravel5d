<?php

namespace Database\Seeders;

use App\Models\Achievement;
use App\Models\Category;
use App\Models\Habit;
use App\Models\HabitLog;
use App\Models\JournalEntry;
use App\Models\MoodEntry;
use App\Models\Profile;
use App\Models\Reminder;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([CategorySeeder::class, AchievementSeeder::class]);

        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
        Profile::factory()->create(['user_id' => $user->id]);

        $tags = collect(['morning', 'evening', 'quick', 'outdoor'])
            ->map(fn (string $name) => Tag::firstOrCreate(['name' => $name]));

        $categories = Category::all();

        foreach ($categories as $category) {
            $habit = Habit::factory()->create([
                'user_id' => $user->id,
                'category_id' => $category->id,
                'name' => $category->name.' habit',
            ]);

            $habit->tags()->attach($tags->random(2)->pluck('id'));
            Reminder::factory()->create(['habit_id' => $habit->id]);

            foreach (range(0, 13) as $daysAgo) {
                HabitLog::factory()->create([
                    'habit_id' => $habit->id,
                    'logged_date' => now()->subDays($daysAgo)->toDateString(),
                ]);
            }
        }

        foreach (range(0, 13) as $daysAgo) {
            $mood = MoodEntry::factory()->create([
                'user_id' => $user->id,
                'entry_date' => now()->subDays($daysAgo)->toDateString(),
            ]);

            if ($daysAgo % 3 === 0) {
                JournalEntry::factory()->create([
                    'user_id' => $user->id,
                    'mood_entry_id' => $mood->id,
                    'entry_date' => $mood->entry_date,
                ]);
            }
        }

        $user->achievements()->attach(Achievement::first()->id);
    }
}
