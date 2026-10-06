<?php

namespace Database\Seeders;

use App\Models\InspectionReport;
use App\Models\Invoice;
use App\Models\Mechanic;
use App\Models\Promotion;
use App\Models\ServiceBooking;
use App\Models\ServicePackage;
use App\Models\ServiceReview;
use App\Models\SparePart;
use App\Models\User;
use App\Models\UserProfile;
use App\Models\Vehicle;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed Master Data
        $this->call([
            ServiceCategorySeeder::class,
            SparePartSeeder::class,
            MechanicSeeder::class,
            PromotionSeeder::class,
        ]);

        // 2. Seed Primary Student / Demo User
        $user = User::create([
            'name' => 'Muhammad Khairil Ilham',
            'email' => 'khairil@example.com',
            'password' => Hash::make('password123'),
        ]);

        // 1:1 Relationship User -> UserProfile
        UserProfile::create([
            'user_id' => $user->id,
            'phone_number' => '082155667788',
            'address' => 'Jl. A. Yani Km 36, Banjarbaru',
            'city' => 'Banjarbaru',
            'postal_code' => '70714',
            'avatar_url' => 'https://ui-avatars.com/api/?name=Muhammad+Khairil+Ilham&background=0D8ABC&color=fff',
        ]);

        // 1:N Relationship User -> Vehicles
        $vehicle1 = Vehicle::create([
            'user_id' => $user->id,
            'plate_number' => 'DA 1205 KI',
            'brand' => 'Toyota',
            'model' => 'Innova Reborn Diesel',
            'production_year' => 2021,
            'color' => 'Hitam Metalik',
            'transmission_type' => 'Automatic',
        ]);

        $vehicle2 = Vehicle::create([
            'user_id' => $user->id,
            'plate_number' => 'DA 5432 AB',
            'brand' => 'Honda',
            'model' => 'HR-V Prestige',
            'production_year' => 2022,
            'color' => 'Putih Mutiara',
            'transmission_type' => 'CVT',
        ]);

        // N:M with Pivot: User -> Promotion
        $promo = Promotion::first();
        if ($promo) {
            $user->promotions()->attach($promo->id, [
                'discount_applied' => 50000,
                'used_at' => now(),
            ]);
        }

        // Master records for booking
        $mechanic = Mechanic::first();
        $packages = ServicePackage::take(2)->get();
        $parts = SparePart::take(2)->get();

        // 1:N Vehicle -> ServiceBooking
        $booking = ServiceBooking::create([
            'booking_code' => 'SB-202610-001',
            'vehicle_id' => $vehicle1->id,
            'mechanic_id' => $mechanic->id,
            'booking_date' => now()->subDays(2)->format('Y-m-d'),
            'booking_time' => '09:30:00',
            'status' => 'Completed',
            'customer_notes' => 'Tolong cek getaran rem pada kecepatan tinggi dan kuras oli mesin.',
        ]);

        // N:M with Pivot: ServiceBooking <-> ServicePackage
        foreach ($packages as $pkg) {
            $booking->servicePackages()->attach($pkg->id, [
                'package_price' => $pkg->base_price,
                'technician_notes' => 'Selesai dikerjakan sesuai SOP bengkel resmi.',
            ]);
        }

        // N:M with Pivot: ServiceBooking <-> SparePart
        $totalParts = 0;
        foreach ($parts as $part) {
            $qty = 1;
            $subtotal = $part->unit_price * $qty;
            $totalParts += $subtotal;

            $booking->spareParts()->attach($part->id, [
                'quantity' => $qty,
                'unit_price' => $part->unit_price,
                'subtotal_price' => $subtotal,
            ]);
        }

        $totalServices = $packages->sum('base_price');
        $discount = 50000;
        $grandTotal = ($totalServices + $totalParts) - $discount;

        // 1:1 ServiceBooking -> InspectionReport
        InspectionReport::create([
            'service_booking_id' => $booking->id,
            'odometer_km' => 45200,
            'fuel_level' => 'Three Quarters',
            'brake_condition' => 'Needs Replacement',
            'tire_condition' => 'Good',
            'battery_condition' => 'Good',
            'inspector_notes' => 'Ketebalan kampas rem depan di bawah batas minimal, disarankan penggantian.',
        ]);

        // 1:1 ServiceBooking -> Invoice
        Invoice::create([
            'invoice_number' => 'INV/20261005/0001',
            'service_booking_id' => $booking->id,
            'total_services_amount' => $totalServices,
            'total_parts_amount' => $totalParts,
            'discount_amount' => $discount,
            'grand_total' => $grandTotal,
            'payment_method' => 'QRIS',
            'payment_status' => 'Paid',
            'paid_at' => now()->subDays(2),
        ]);

        // 1:N ServiceBooking -> ServiceReview
        ServiceReview::create([
            'service_booking_id' => $booking->id,
            'rating' => 5,
            'review_text' => 'Pengerjaan sangat teliti oleh Mas Bambang, rem kembali pakem dan mobil halus kembali!',
        ]);

        // 3. Second booking (In Progress) for vehicle 2
        $mechanic2 = Mechanic::skip(1)->first() ?? $mechanic;
        $booking2 = ServiceBooking::create([
            'booking_code' => 'SB-202610-002',
            'vehicle_id' => $vehicle2->id,
            'mechanic_id' => $mechanic2->id,
            'booking_date' => now()->format('Y-m-d'),
            'booking_time' => '13:00:00',
            'status' => 'In Progress',
            'customer_notes' => 'Pemeriksaan AC tidak dingin saat siang hari.',
        ]);

        $acPkg = ServicePackage::where('name', 'like', '%AC%')->first();
        if ($acPkg) {
            $booking2->servicePackages()->attach($acPkg->id, [
                'package_price' => $acPkg->base_price,
                'technician_notes' => 'Sedang dalam proses pengecekan tekanan freon.',
            ]);
        }

        InspectionReport::create([
            'service_booking_id' => $booking2->id,
            'odometer_km' => 28400,
            'fuel_level' => 'Half',
            'brake_condition' => 'Good',
            'tire_condition' => 'Good',
            'battery_condition' => 'Fair',
            'inspector_notes' => 'Tekanan angin ban telah disesuaikan standard (32 PSI).',
        ]);
    }
}
