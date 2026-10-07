<?php

namespace Database\Factories;

use App\Models\CustomerReview;
use App\Models\LaundryOrder;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerReviewFactory extends Factory
{
    protected $model = CustomerReview::class;

    public function definition(): array
    {
        return [
            'laundry_order_id' => LaundryOrder::factory(),
            'customer_id' => User::factory()->state(['role' => 'customer']),
            'rating' => $this->faker->numberBetween(4, 5),
            'comment' => $this->faker->randomElement([
                'Cepat sekali cuciannya bersih dan wangi!',
                'Pelayanan ramah, pakaian rapi terlipat sempurna.',
                'Sangat puas dengan layanan Laundry Express.',
            ]),
        ];
    }
}
