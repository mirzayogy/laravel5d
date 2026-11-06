<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(PeriodeSeeder::class);
        $this->call(IndonesianMenuSeeder::class);
        $this->call(DemoDataSeeder::class);
    }
}
