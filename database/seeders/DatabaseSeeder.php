<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Pet;
use App\Models\Service;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\GroomerProfile;
use App\Models\GroomingRecord;
use App\Models\BookingStatusHistory;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Layanan grooming
        $this->call(ServiceSeeder::class);

        // 2. Ambil pengguna yang sudah ada
        $admin = User::where('role', 'admin')->firstOrFail();

        $customers = User::where('role', 'customer')->get();
        $groomers = User::where('role', 'groomer')->get();

        if ($customers->isEmpty() || $groomers->isEmpty()) {
            throw new \RuntimeException(
                'Customer atau groomer belum tersedia.'
            );
        }

        // 3. Buat profil groomer
        foreach ($groomers as $groomer) {
            if (!GroomerProfile::where('user_id', $groomer->id)->exists()) {
                GroomerProfile::factory()->create([
                    'user_id' => $groomer->id,
                ]);
            }
        }

        // 4. Buat dua hewan untuk setiap customer
        foreach ($customers as $customer) {
            $existing = Pet::where('user_id', $customer->id)->count();

            $needed = max(0, 2 - $existing);

            if ($needed > 0) {
                Pet::factory()->count($needed)->create([
                    'user_id' => $customer->id,
                ]);
            }
        }

        // 5. Ambil hewan dan layanan aktif
        $pets = Pet::whereIn(
            'user_id',
            $customers->pluck('id')
        )->get();

        $services = Service::where('is_active', true)->get();

        if ($pets->isEmpty() || $services->isEmpty()) {
            throw new \RuntimeException(
                'Hewan atau layanan belum tersedia.'
            );
        }

        // 6. Buat maksimal 15 booking demo
        for ($i = 1; $i <= 15; $i++) {
            $code = 'PAW-DEMO-'.str_pad($i, 4, '0', STR_PAD_LEFT);

            if (Booking::where('booking_code', $code)->exists()) {
                continue;
            }

            $pet = $pets->random();
            $service = $services->random();

            $status = match ($i % 4) {
                0 => 'completed',
                1 => 'pending',
                2 => 'confirmed',
                default => 'in_progress',
            };

            $groomer = $status === 'pending'
                ? null
                : $groomers->random();

            $start = Carbon::today()
                ->addDays($i)
                ->setTime(9, 0);

            $booking = Booking::factory()->create([
                'booking_code' => $code,
                'user_id' => $pet->user_id,
                'pet_id' => $pet->id,
                'service_id' => $service->id,
                'groomer_id' => $groomer?->id,
                'scheduled_at' => $start,
                'ends_at' => $start->copy()->addMinutes(
                    $service->duration_minutes
                ),
                'total_price' => $service->price,
                'status' => $status,
            ]);

            // 7. Pembayaran
            Payment::factory()->create([
                'booking_id' => $booking->id,
                'method' => 'cash',
                'amount' => $booking->total_price,
                'status' => $status === 'completed'
                    ? 'paid'
                    : 'unpaid',
                'paid_at' => $status === 'completed'
                    ? now()
                    : null,
                'verified_by' => $status === 'completed'
                    ? $admin->id
                    : null,
                'verified_at' => $status === 'completed'
                    ? now()
                    : null,
            ]);

            // 8. Riwayat status awal
            BookingStatusHistory::factory()->create([
                'booking_id' => $booking->id,
                'changed_by' => $pet->user_id,
                'old_status' => null,
                'new_status' => 'pending',
            ]);

            if ($status !== 'pending') {
                BookingStatusHistory::factory()->create([
                    'booking_id' => $booking->id,
                    'changed_by' => $admin->id,
                    'old_status' => 'pending',
                    'new_status' => 'confirmed',
                ]);
            }

            if (in_array($status, ['in_progress', 'completed'])) {
                BookingStatusHistory::factory()->create([
                    'booking_id' => $booking->id,
                    'changed_by' => $groomer->id,
                    'old_status' => 'confirmed',
                    'new_status' => 'in_progress',
                ]);
            }

            // 9. Catatan grooming untuk booking selesai
            if ($status === 'completed') {
                GroomingRecord::factory()->create([
                    'booking_id' => $booking->id,
                    'groomer_id' => $groomer->id,
                    'completed_at' => now(),
                ]);

                BookingStatusHistory::factory()->create([
                    'booking_id' => $booking->id,
                    'changed_by' => $groomer->id,
                    'old_status' => 'in_progress',
                    'new_status' => 'completed',
                ]);
            }
        }

        $this->command->info(
            'Seeder PawCare berhasil dijalankan!'
        );
    }
}