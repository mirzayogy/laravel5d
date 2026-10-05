<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Field;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BookingSeeder extends Seeder
{
    public function run(): void
    {
        Booking::factory(10)
            ->state(fn () => [
                'user_id' => User::inRandomOrder()->value('id'),
                'field_id' => Field::inRandomOrder()->value('id'),
            ])
            ->create();
    }
}