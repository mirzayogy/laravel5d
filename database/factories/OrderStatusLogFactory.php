<?php

namespace Database\Factories;

use App\Models\LaundryOrder;
use App\Models\OrderStatusLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderStatusLogFactory extends Factory
{
    protected $model = OrderStatusLog::class;

    public function definition(): array
    {
        return [
            'laundry_order_id' => LaundryOrder::factory(),
            'staff_id' => User::factory()->state(['role' => 'staff']),
            'previous_status' => 'received',
            'new_status' => 'washing',
            'notes' => 'Pakaian telah diproses masuk ke mesin cuci 1',
        ];
    }
}
