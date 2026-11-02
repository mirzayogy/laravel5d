<?php

namespace Database\Seeders;

use App\Models\Addon;
use App\Models\Booking;
use App\Models\Game;
use App\Models\Platform;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ---- Platform ----
        $pc   = Platform::create(['name' => 'PC',            'slug' => 'pc',   'description' => 'Gaming PC spesifikasi tinggi']);
        $ps5  = Platform::create(['name' => 'PlayStation 5', 'slug' => 'ps5',  'description' => 'Konsol PS5 dengan TV 4K']);
        $xbox = Platform::create(['name' => 'Xbox Series X', 'slug' => 'xbox', 'description' => 'Konsol Xbox Series X']);
        $vr   = Platform::create(['name' => 'VR',            'slug' => 'vr',   'description' => 'Virtual reality headset']);

        // ---- Station (One-to-Many dari Platform) ----
        $pc1 = $pc->stations()->create(['name' => 'PC-01',   'price_per_hour' => 10000]);
        $pc2 = $pc->stations()->create(['name' => 'PC-02',   'price_per_hour' => 10000]);
        $ps1 = $ps5->stations()->create(['name' => 'PS5-01', 'price_per_hour' => 15000]);
        $xb1 = $xbox->stations()->create(['name' => 'XBOX-01', 'price_per_hour' => 15000]);
        $vr1 = $vr->stations()->create(['name' => 'VR-01',   'price_per_hour' => 25000]);

        // ---- Game & pivot game_station (Many-to-Many) ----
        $valorant = Game::create(['title' => 'Valorant',   'genre' => 'FPS']);
        $dota     = Game::create(['title' => 'Dota 2',     'genre' => 'MOBA']);
        $fc       = Game::create(['title' => 'EA FC 26',   'genre' => 'Sport']);
        $tekken   = Game::create(['title' => 'Tekken 8',   'genre' => 'Fighting']);
        $beat     = Game::create(['title' => 'Beat Saber', 'genre' => 'Rhythm']);

        $pc1->games()->attach([$valorant->id, $dota->id]);
        $pc2->games()->attach([$valorant->id, $dota->id]);
        $ps1->games()->attach([$fc->id, $tekken->id]);
        $xb1->games()->attach([$fc->id]);
        $vr1->games()->attach([$beat->id]);

        // ---- Addon ----
        $drink = Addon::create(['name' => 'Minuman',         'price' => 5000]);
        $snack = Addon::create(['name' => 'Snack',           'price' => 8000]);
        $ctrl  = Addon::create(['name' => 'Sewa Controller', 'price' => 10000]);

        // ---- User + Profile (One-to-One) ----
        $hersa = User::create([
            'name'     => 'Muhammad Hersa Sugiannor',
            'email'    => 'hersa@example.com',
            'password' => Hash::make('password'),
        ]);
        $hersa->profile()->create(['nickname' => 'Hersa', 'gamer_tag' => 'Hersa7', 'phone' => '081234567890']);

        $budi = User::create([
            'name'     => 'Budi Santoso',
            'email'    => 'budi@example.com',
            'password' => Hash::make('password'),
        ]);
        $budi->profile()->create(['nickname' => 'Budi', 'gamer_tag' => 'BudiPro', 'phone' => '081298765432']);

        // ---- Booking + Payment (One-to-One) + Addon (pivot dengan data) ----
        $b1 = Booking::create([
            'user_id'      => $hersa->id,
            'station_id'   => $pc1->id,
            'booking_date' => '2026-10-10',
            'start_time'   => '14:00',
            'end_time'     => '16:00',
            'status'       => 'done',
            'total_price'  => 20000 + 5000 * 2,
        ]);
        $b1->addons()->attach($drink->id, ['quantity' => 2, 'unit_price' => $drink->price]);
        $b1->payment()->create([
            'method'  => 'QRIS',
            'amount'  => $b1->total_price,
            'status'  => 'paid',
            'paid_at' => '2026-10-10 14:00:00',
        ]);

        $b2 = Booking::create([
            'user_id'      => $hersa->id,
            'station_id'   => $ps1->id,
            'booking_date' => '2026-10-12',
            'start_time'   => '19:00',
            'end_time'     => '22:00',
            'status'       => 'confirmed',
            'total_price'  => 45000 + 8000 + 10000,
        ]);
        $b2->addons()->attach([
            $snack->id => ['quantity' => 1, 'unit_price' => $snack->price],
            $ctrl->id  => ['quantity' => 1, 'unit_price' => $ctrl->price],
        ]);
        $b2->payment()->create([
            'method' => 'Transfer',
            'amount' => $b2->total_price,
            'status' => 'unpaid',
        ]);

        $b3 = Booking::create([
            'user_id'      => $budi->id,
            'station_id'   => $vr1->id,
            'booking_date' => '2026-10-11',
            'start_time'   => '10:00',
            'end_time'     => '11:00',
            'status'       => 'pending',
            'total_price'  => 25000,
        ]);
        $b3->payment()->create([
            'method' => 'Cash',
            'amount' => $b3->total_price,
            'status' => 'unpaid',
        ]);

        // ---- Review ----
        Review::create(['user_id' => $hersa->id, 'station_id' => $pc1->id, 'rating' => 5, 'comment' => 'PC lancar, internet stabil.']);
        Review::create(['user_id' => $budi->id,  'station_id' => $pc1->id, 'rating' => 4, 'comment' => 'Nyaman dan bersih.']);
        $this->call(PeriodeSeeder::class);
    }
}
