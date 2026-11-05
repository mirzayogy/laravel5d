<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Facility;
use App\Models\Kost;
use App\Models\Payment;
use App\Models\Room;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;

class KostAreaSeeder extends Seeder
{
    public function run(): void
    {
        // Pemilik kost
        $owner = User::factory()->create([
            'name' => 'Pemilik Kost',
            'email' => 'pemilik@kostarea.test',
        ]);

        // Fasilitas (data tetap)
        $facilities = collect([
            'AC',
            'WiFi',
            'Kamar Mandi Dalam',
            'Kasur',
            'Lemari',
            'Parkir Motor',
        ])->map(fn ($name) => Facility::create(['name' => $name]));

        // Dua kost: khusus putra dan khusus putri
        $kosts = [
            [
                'name' => 'Kost Area Putra',
                'type' => 'putra',
                'gender' => 'laki-laki',
                'address' => 'Jl. Pangeran Antasari No. 10',
                'description' => 'Kost khusus putra dengan fasilitas lengkap dan dekat kampus.',
            ],
            [
                'name' => 'Kost Area Putri',
                'type' => 'putri',
                'gender' => 'perempuan',
                'address' => 'Jl. Pangeran Antasari No. 12',
                'description' => 'Kost khusus putri yang aman dan nyaman, dekat kampus.',
            ],
        ];

        $counter = 0;

        foreach ($kosts as $data) {
            $kost = Kost::create([
                'user_id' => $owner->id,
                'name' => $data['name'],
                'type' => $data['type'],
                'address' => $data['address'],
                'city' => 'Banjarmasin',
                'description' => $data['description'],
            ]);

            // 5 kamar per kost: 3 terisi, 1 tersedia, 1 perbaikan
            foreach (range(1, 5) as $i) {
                $room = Room::factory()->create([
                    'kost_id' => $kost->id,
                    'room_number' => (string) (100 + $i),
                    'status' => match (true) {
                        $i <= 3 => 'occupied',
                        $i === 4 => 'available',
                        default => 'maintenance',
                    },
                ]);

                $room->facilities()->attach($facilities->random(3)->pluck('id'));

                if ($i > 3) {
                    continue;
                }

                // Kamar terisi: penyewa harus sesuai jenis kost
                $tenant = Tenant::factory()->create(['gender' => $data['gender']]);

                $daily = $i === 3;
                $duration = $daily ? 5 : 6;
                $price = $daily ? $room->price_per_day : $room->price_per_month;
                $start = $daily ? now()->subDays(2)->startOfDay() : now()->subMonths(2)->startOfDay();
                $end = $daily ? $start->copy()->addDays($duration) : $start->copy()->addMonths($duration);

                $booking = Booking::create([
                    'room_id' => $room->id,
                    'tenant_id' => $tenant->id,
                    'code' => sprintf('BK-%s-%03d', now()->format('ym'), ++$counter),
                    'rental_type' => $daily ? 'daily' : 'monthly',
                    'start_date' => $start->toDateString(),
                    'end_date' => $end->toDateString(),
                    'duration' => $duration,
                    'price_per_unit' => $price,
                    'deposit_amount' => $daily ? 0 : $room->price_per_month,
                    'total_price' => $price * $duration,
                    'checked_in_at' => $start->copy()->setTime(14, 0),
                    'status' => 'active',
                ]);

                $pay = fn (array $attrs) => Payment::create($attrs + ['booking_id' => $booking->id]);

                // Sewa harian: tanpa deposit, satu pembayaran sebesar total, dibayar QRIS
                if ($daily) {
                    $pay([
                        'type' => 'rent',
                        'amount' => $booking->total_price,
                        'due_date' => $start->toDateString(),
                        'method' => 'qris',
                        'status' => 'paid',
                        'proof_path' => 'bukti-bayar/contoh.jpg',
                        'paid_at' => $start->copy()->setTime(10, 0),
                        'verified_by' => $owner->id,
                        'verified_at' => $start->copy()->setTime(11, 0),
                    ]);

                    continue;
                }

                $rent = $room->price_per_month;

                // Deposit: lunas
                $pay([
                    'type' => 'deposit',
                    'amount' => $rent,
                    'due_date' => $start->toDateString(),
                    'method' => 'transfer',
                    'status' => 'paid',
                    'proof_path' => 'bukti-bayar/contoh.jpg',
                    'paid_at' => $start->copy()->setTime(9, 0),
                    'verified_by' => $owner->id,
                    'verified_at' => $start->copy()->setTime(10, 0),
                ]);

                // Sewa bulan ke-1: lunas tunai
                $pay([
                    'type' => 'rent',
                    'amount' => $rent,
                    'due_date' => $start->toDateString(),
                    'method' => 'cash',
                    'status' => 'paid',
                    'paid_at' => $start->copy()->setTime(9, 30),
                    'verified_by' => $owner->id,
                    'verified_at' => $start->copy()->setTime(9, 30),
                ]);

                if ($i === 1) {
                    // Sewa bulan ke-2: sudah transfer, menunggu verifikasi pemilik
                    $pay([
                        'type' => 'rent',
                        'amount' => $rent,
                        'due_date' => $start->copy()->addMonth()->toDateString(),
                        'method' => 'transfer',
                        'status' => 'waiting_verification',
                        'proof_path' => 'bukti-bayar/contoh.jpg',
                        'paid_at' => $start->copy()->addMonth()->setTime(8, 0),
                    ]);
                } else {
                    // Sewa bulan ke-2: terlambat, kena denda
                    $pay([
                        'type' => 'rent',
                        'amount' => $rent,
                        'due_date' => $start->copy()->addMonth()->toDateString(),
                        'status' => 'overdue',
                    ]);
                    $pay([
                        'type' => 'late_fee',
                        'amount' => 50000,
                        'due_date' => $start->copy()->addMonth()->addDays(7)->toDateString(),
                        'status' => 'unpaid',
                        'notes' => 'Denda keterlambatan sewa bulan ke-2',
                    ]);
                }

                // Sewa bulan ke-3: belum dibayar
                $pay([
                    'type' => 'rent',
                    'amount' => $rent,
                    'due_date' => $start->copy()->addMonths(2)->toDateString(),
                    'status' => 'unpaid',
                ]);
            }
        }
    }
}