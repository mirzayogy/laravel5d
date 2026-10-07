<?php

namespace Database\Factories;

use App\Models\LaundryOrder;
use App\Models\StorageRack;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class LaundryOrderFactory extends Factory
{
    protected $model = LaundryOrder::class;

    public function definition(): array
    {
        $weight = $this->faker->randomFloat(2, 2, 10);
        $total = $weight * 8000;

        return [
            'order_code' => 'LND-' . date('Ymd') . '-' . strtoupper($this->faker->bothify('###?')),
            'customer_id' => User::factory()->state(['role' => 'customer']),
            'rack_id' => StorageRack::factory(),
            'total_weight_kg' => $weight,
            'total_amount' => $total,
            'discount_amount' => 0,
            'final_amount' => $total,
            'status' => $this->faker->randomElement(['received', 'washing', 'ironing', 'ready_for_pickup', 'completed']),
            'pickup_deadline' => now()->addDays(2),
            'completed_at' => null,
        ];
    }
}
