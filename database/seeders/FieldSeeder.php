<?php

namespace Database\Seeders;

use App\Models\Field;
use App\Models\Venue;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FieldSeeder extends Seeder
{
    public function run(): void
    {
        Venue::all()->each(function (Venue $venue) {
            Field::factory(3)->create(['venue_id' => $venue->id]);
        });
    }
}