<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::factory()->admin()->create([
            'name' => 'Admin PartsHub',
            'email' => 'admin@partshub.test',
        ]);
        $admin->profile()->create(['phone' => '081200000000', 'bio' => 'Administrator PartsHub']);

        foreach (['Techno Parts', 'Hardware Banjar'] as $i => $store) {
            $seller = User::factory()->seller()->create(['email' => 'seller' . ($i + 1) . '@partshub.test']);
            $seller->profile()->create([
                'phone' => fake()->phoneNumber(),
                'store_name' => $store,
                'bio' => 'Penjual komponen PC.',
            ]);
        }

        foreach (range(1, 3) as $i) {
            $buyer = User::factory()->create(['email' => "buyer{$i}@partshub.test"]);
            $buyer->profile()->create(['phone' => fake()->phoneNumber(), 'bio' => fake()->sentence()]);
            Address::factory()->create(['user_id' => $buyer->id, 'is_default' => true]);
        }
    }
}