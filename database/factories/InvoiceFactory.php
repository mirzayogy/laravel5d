<?php

namespace Database\Factories;

use App\Models\Invoice;
use App\Models\ServiceBooking;
use Illuminate\Database\Eloquent\Factories\Factory;

class InvoiceFactory extends Factory
{
    protected $model = Invoice::class;

    public function definition(): array
    {
        $services = fake()->randomFloat(2, 100000, 500000);
        $parts = fake()->randomFloat(2, 50000, 400000);
        $discount = fake()->randomElement([0, 25000, 50000]);
        $total = ($services + $parts) - $discount;

        return [
            'invoice_number' => 'INV/' . date('Ymd') . '/' . fake()->unique()->numerify('####'),
            'service_booking_id' => ServiceBooking::factory(),
            'total_services_amount' => $services,
            'total_parts_amount' => $parts,
            'discount_amount' => $discount,
            'grand_total' => max($total, 0),
            'payment_method' => fake()->randomElement(['Cash', 'Transfer', 'QRIS', 'Credit Card']),
            'payment_status' => 'Paid',
            'paid_at' => now(),
        ];
    }
}
