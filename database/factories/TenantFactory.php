<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class TenantFactory extends Factory
{
    public function definition(): array
    {
        $gender = fake()->randomElement(['laki-laki', 'perempuan']);

        return [
            'name' => fake()->name($gender === 'laki-laki' ? 'male' : 'female'),
            'gender' => $gender,
            'phone' => '08' . fake()->numerify('##########'),
            'id_number' => fake()->unique()->numerify('################'),
            'occupation' => fake()->randomElement(['Mahasiswa', 'Karyawan', 'Wiraswasta', 'Guru']),
        ];
    }
}