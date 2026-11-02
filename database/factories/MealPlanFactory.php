<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class MealPlanFactory extends Factory
{
    public function definition(): array
    {
        $start = now()->startOfWeek();

        return [
            'user_id' => User::factory(),
            'name' => 'Weekly Plan ' . $start->format('d M'),
            'start_date' => $start->toDateString(),
            'end_date' => $start->copy()->addDays(6)->toDateString(),
        ];
    }
}
