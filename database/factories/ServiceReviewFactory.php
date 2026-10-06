<?php

namespace Database\Factories;

use App\Models\ServiceBooking;
use App\Models\ServiceReview;
use Illuminate\Database\Eloquent\Factories\Factory;

class ServiceReviewFactory extends Factory
{
    protected $model = ServiceReview::class;

    public function definition(): array
    {
        return [
            'service_booking_id' => ServiceBooking::factory(),
            'rating' => fake()->numberBetween(4, 5),
            'review_text' => fake()->randomElement([
                'Pelayanan ramah, pengerjaan cepat dan rapi. Sangat puas!',
                'Mekanik sangat berpengalaman, tarikan mobil jadi enteng lagi.',
                'Tempat servis nyaman, ruang tunggu ber-AC, harga transparan.',
                'Suku cadang original dan bergaransi, recommended.',
            ]),
        ];
    }
}
