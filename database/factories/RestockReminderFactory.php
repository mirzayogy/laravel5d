<?php

namespace Database\Factories;

use App\Models\Fish;
use App\Models\RestockReminder;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<RestockReminder>
 */
class RestockReminderFactory extends Factory
{
    public function definition(): array
    {
        return [
            'fish_id' => Fish::factory(),
            'remind_at' => fake()->time('H:i:s'),
            'days_of_week' => ['mon', 'wed', 'fri'],
            'is_enabled' => true,
        ];
    }
}
