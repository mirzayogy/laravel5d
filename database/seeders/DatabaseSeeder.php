<?php

namespace Database\Seeders;

use App\Models\Badge;
use App\Models\Category;
use App\Models\CustomerReview;
use App\Models\Fish;
use App\Models\Profile;
use App\Models\RestockReminder;
use App\Models\SalesLog;
use App\Models\SalesReport;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database with Penjualan Ikan sample data.
     */
    public function run(): void
    {
        $this->call([CategorySeeder::class, AchievementSeeder::class]);

        $user = User::factory()->create([
            'name' => 'Riezky Kurniawan',
            'email' => 'riezkykurniawan2000@gmail.com',
        ]);

        Profile::factory()->create([
            'user_id' => $user->id,
            'bio' => 'Pemilik Toko Penjualan Ikan Segar & Hias Berkualitas',
            'timezone' => 'Asia/Makassar',
            'daily_goal' => 50,
        ]);

        $tags = collect(['segar', 'terlaris', 'diskon', 'grade_A', 'bibit_unggul', 'impor'])
            ->map(fn (string $name) => Tag::firstOrCreate(['name' => $name]));

        $sampleProducts = [
            'ikan-hias' => ['Ikan Cupang Halfmoon Red', 'ekor'],
            'ikan-konsumsi' => ['Ikan Gurame Segar Super', 'kg'],
            'ikan-laut' => ['Ikan Kerapu Bintang Laut', 'kg'],
            'ikan-tawar' => ['Ikan Nila Merah Fresh', 'kg'],
            'bibit-benih-ikan' => ['Bibit Ikan Nila Rajadanu (5cm)', 'ekor'],
            'pakan-ikan' => ['Pelet Ikan Feng Li Premium 1kg', 'paket'],
            'perlengkapan-akuarium' => ['Filter Submersible 1200L/H', 'unit'],
            'obat-nutrisi-ikan' => ['Obat Anti Jamur Ikan 100ml', 'botol'],
        ];

        $categories = Category::all();

        foreach ($categories as $category) {
            [$productName, $unit] = $sampleProducts[$category->slug] ?? ['Produk Ikan '.$category->name, 'ekor'];

            $fish = Fish::factory()->create([
                'user_id' => $user->id,
                'category_id' => $category->id,
                'name' => $productName,
                'unit' => $unit,
                'stock' => rand(10, 100),
            ]);

            $fish->tags()->attach($tags->random(2)->pluck('id'));

            RestockReminder::factory()->create([
                'fish_id' => $fish->id,
                'remind_at' => '08:00:00',
                'is_enabled' => true,
            ]);

            foreach (range(0, 13) as $daysAgo) {
                SalesLog::factory()->create([
                    'fish_id' => $fish->id,
                    'logged_date' => now()->subDays($daysAgo)->toDateString(),
                    'value' => rand(5, 25),
                    'note' => 'Penjualan '.$productName.' tanggal '.now()->subDays($daysAgo)->format('d M Y'),
                ]);
            }
        }

        foreach (range(0, 13) as $daysAgo) {
            $review = CustomerReview::factory()->create([
                'user_id' => $user->id,
                'entry_date' => now()->subDays($daysAgo)->toDateString(),
                'rating' => rand(4, 5),
                'note' => 'Ulasan kepuasan pelanggan hari ke-'.($daysAgo + 1),
            ]);

            if ($daysAgo % 3 === 0) {
                SalesReport::factory()->create([
                    'user_id' => $user->id,
                    'customer_review_id' => $review->id,
                    'entry_date' => $review->entry_date,
                    'title' => 'Laporan Rekap Penjualan Ikan Tanggal '.$review->entry_date->format('d M Y'),
                    'content' => 'Laporan transaksi penjualan toko ikan berjalan dengan sangat lancar dan stok produk aman.',
                ]);
            }
        }

        $user->badges()->attach(Badge::first()->id, ['earned_at' => now()]);
    }
}
