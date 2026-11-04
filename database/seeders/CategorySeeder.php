<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Seed the built-in habit categories.
     */
    public function run(): void
    {
        $categories = [
            ['Health & Fitness', 'health-fitness', '💪', '#ef4444'],
            ['Mindfulness', 'mindfulness', '🧘', '#8b5cf6'],
            ['Productivity', 'productivity', '🎯', '#f59e0b'],
            ['Better Sleep', 'better-sleep', '😴', '#6366f1'],
            ['Stay Hydrated', 'stay-hydrated', '💧', '#0ea5e9'],
            ['Read More', 'read-more', '📚', '#10b981'],
            ['Social Connections', 'social-connections', '🤝', '#ec4899'],
            ['Self Care', 'self-care', '🌸', '#f97316'],
        ];

        foreach ($categories as [$name, $slug, $icon, $color]) {
            Category::updateOrCreate(['slug' => $slug], compact('name', 'icon', 'color'));
        }
    }
}
