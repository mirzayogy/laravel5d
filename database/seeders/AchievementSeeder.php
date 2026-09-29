<?php

namespace Database\Seeders;

use App\Models\Achievement;
use Illuminate\Database\Seeder;

class AchievementSeeder extends Seeder
{
    /**
     * Seed the built-in achievements.
     */
    public function run(): void
    {
        $achievements = [
            ['First Step', 'Log your first habit.', 'logs:1'],
            ['7-Day Streak', 'Keep a habit going for 7 days in a row.', 'streak:7'],
            ['30-Day Streak', 'Keep a habit going for 30 days in a row.', 'streak:30'],
            ['Mood Master', 'Record your mood for 14 days.', 'moods:14'],
            ['Dear Diary', 'Write 10 journal entries.', 'journals:10'],
        ];

        foreach ($achievements as [$name, $description, $criteria]) {
            Achievement::updateOrCreate(['name' => $name], compact('description', 'criteria'));
        }
    }
}
