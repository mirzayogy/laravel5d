<?php

namespace Database\Factories;

use App\Models\InspectionReport;
use App\Models\ServiceBooking;
use Illuminate\Database\Eloquent\Factories\Factory;

class InspectionReportFactory extends Factory
{
    protected $model = InspectionReport::class;

    public function definition(): array
    {
        return [
            'service_booking_id' => ServiceBooking::factory(),
            'odometer_km' => fake()->numberBetween(5000, 150000),
            'fuel_level' => fake()->randomElement(['Empty', 'Quarter', 'Half', 'Three Quarters', 'Full']),
            'brake_condition' => fake()->randomElement(['Good', 'Fair', 'Needs Replacement']),
            'tire_condition' => fake()->randomElement(['Good', 'Fair', 'Needs Replacement']),
            'battery_condition' => fake()->randomElement(['Good', 'Fair', 'Weak']),
            'inspector_notes' => 'Pemeriksaan fisik awal saat check-in kendaraan.',
        ];
    }
}
