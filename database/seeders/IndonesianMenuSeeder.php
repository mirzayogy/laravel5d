<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Menu;
use App\Models\Tag;
use Illuminate\Database\Seeder;

/**
 * Menu typical of Indonesian cafes: [name, description, price, tags].
 */
class IndonesianMenuSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(CategorySeeder::class);

        $tags = collect(['Pedas', 'Vegetarian', 'Dingin', 'Panas', 'Manis', 'Best Seller'])
            ->mapWithKeys(fn (string $name) => [$name => Tag::firstOrCreate(['name' => $name])]);

        $menus = [
            'Kopi' => [
                ['Kopi Susu Gula Aren', 'Espresso, susu segar, dan gula aren asli.', 22000, ['Dingin', 'Manis', 'Best Seller']],
                ['Kopi Tubruk', 'Kopi hitam seduh tradisional dengan ampas, kuat dan harum.', 12000, ['Panas']],
                ['Es Kopi Susu', 'Kopi susu dingin dengan sirup gula cair.', 20000, ['Dingin', 'Manis']],
                ['Americano', 'Espresso double dengan air panas, tanpa gula.', 18000, ['Panas']],
                ['Cappuccino', 'Espresso dengan susu dan foam tebal.', 24000, ['Panas']],
                ['Kopi Vietnam Drip', 'Kopi tetes dengan susu kental manis.', 22000, ['Dingin', 'Manis']],
            ],
            'Non-Kopi' => [
                ['Es Cokelat', 'Cokelat pekat dengan susu dingin dan es batu.', 22000, ['Dingin', 'Manis']],
                ['Matcha Latte', 'Matcha Jepang dengan susu segar.', 25000, ['Dingin']],
                ['Wedang Jahe', 'Jahe merah, gula batu, dan serai, disajikan hangat.', 14000, ['Panas', 'Vegetarian']],
                ['Es Kelapa Muda', 'Air dan daging kelapa muda dengan sirup.', 16000, ['Dingin', 'Vegetarian']],
                ['Es Jeruk Peras', 'Jeruk peras segar tanpa pengawet.', 15000, ['Dingin', 'Vegetarian']],
                ['Jus Alpukat', 'Alpukat mentega dengan susu cokelat.', 20000, ['Dingin', 'Manis', 'Best Seller']],
            ],
            'Teh' => [
                ['Es Teh Manis', 'Teh melati seduh dengan gula dan es batu.', 8000, ['Dingin', 'Manis']],
                ['Teh Tawar Hangat', 'Teh melati seduh tanpa gula.', 6000, ['Panas']],
                ['Es Lemon Tea', 'Teh dingin dengan perasan lemon.', 14000, ['Dingin']],
                ['Teh Tarik', 'Teh susu yang ditarik hingga berbusa.', 15000, ['Panas', 'Manis']],
                ['Thai Tea', 'Teh Thailand dengan susu dan es.', 17000, ['Dingin', 'Manis']],
            ],
            'Camilan' => [
                ['Pisang Goreng', 'Pisang kepok goreng renyah dengan taburan keju.', 15000, ['Manis', 'Vegetarian', 'Best Seller']],
                ['Singkong Goreng', 'Singkong goreng dengan sambal bawang.', 13000, ['Pedas', 'Vegetarian']],
                ['Tempe Mendoan', 'Tempe tipis tepung bumbu, setengah matang, dengan cabai rawit.', 14000, ['Pedas', 'Vegetarian']],
                ['Tahu Crispy', 'Tahu goreng tepung dengan bumbu pedas.', 14000, ['Pedas', 'Vegetarian']],
                ['Cireng Rujak', 'Cireng isi dengan sambal rujak pedas manis.', 15000, ['Pedas']],
                ['Kentang Goreng', 'Kentang goreng dengan saus sambal dan mayones.', 16000, ['Vegetarian']],
                ['Roti Bakar Cokelat Keju', 'Roti tebal panggang dengan cokelat dan keju.', 18000, ['Manis', 'Best Seller']],
                ['Risoles Mayo', 'Risoles isi sayur, telur, dan mayones.', 15000, []],
            ],
            'Makanan Berat' => [
                ['Nasi Goreng Kampung', 'Nasi goreng bumbu kampung dengan telur dan kerupuk.', 28000, ['Pedas', 'Best Seller']],
                ['Mie Goreng Jawa', 'Mie goreng bumbu Jawa dengan sayur dan telur.', 26000, []],
                ['Ayam Penyet', 'Ayam goreng dilumat dengan sambal terasi, nasi, dan lalapan.', 30000, ['Pedas']],
                ['Ayam Geprek', 'Ayam crispy digeprek dengan sambal bawang dan nasi.', 28000, ['Pedas']],
                ['Soto Ayam', 'Soto kuning dengan suwiran ayam, telur, dan nasi.', 27000, ['Panas']],
                ['Gado-Gado', 'Sayuran rebus dengan bumbu kacang dan lontong.', 24000, ['Vegetarian']],
                ['Indomie Telur Kornet', 'Mie instan rebus atau goreng dengan telur dan kornet.', 20000, []],
                ['Sate Ayam Madura', 'Sepuluh tusuk sate ayam dengan bumbu kacang dan lontong.', 32000, []],
            ],
            'Dessert' => [
                ['Es Cendol', 'Cendol, santan, dan gula merah cair.', 15000, ['Dingin', 'Manis', 'Vegetarian']],
                ['Es Teler', 'Alpukat, kelapa, dan nangka dengan susu dan sirup.', 18000, ['Dingin', 'Manis', 'Vegetarian']],
                ['Klepon Cake', 'Bolu pandan isi gula merah dengan kelapa parut.', 20000, ['Manis', 'Best Seller']],
                ['Martabak Manis Mini', 'Martabak mini cokelat, kacang, dan keju.', 22000, ['Manis', 'Panas']],
                ['Pudding Gula Merah', 'Puding susu dengan saus gula merah.', 14000, ['Dingin', 'Manis']],
            ],
        ];

        foreach ($menus as $categoryName => $items) {
            $category = Category::firstOrCreate(['name' => $categoryName]);

            foreach ($items as [$name, $description, $price, $tagNames]) {
                $menu = Menu::updateOrCreate(
                    ['category_id' => $category->id, 'name' => $name],
                    ['description' => $description, 'price' => $price, 'is_available' => true],
                );

                $menu->tags()->sync(collect($tagNames)->map(fn (string $tag) => $tags[$tag]->id)->all());
            }
        }

        // One menu is sold out, so the "Sold out" state is visible in the demo.
        Menu::where('name', 'Sate Ayam Madura')->update(['is_available' => false]);
    }
}
