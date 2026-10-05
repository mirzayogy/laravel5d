<?php

namespace Database\Seeders;

use App\Models\Facility;
use App\Models\Field;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FacilitySeeder extends Seeder
{
    public function run(): void
    {
        foreach (['Toilet', 'Kantin', 'Parkir', 'Musala', 'Ruang Ganti', 'WiFi'] as $name) {
            Facility::firstOrCreate(['name' => $name]);
        }

        Field::all()->each(function (Field $field) {
            $ids = Facility::inRandomOrder()->take(rand(2, 4))->pluck('id');
            $field->facilities()->attach($ids);
        });
    }
}