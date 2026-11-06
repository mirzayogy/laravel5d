<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Menu;
use App\Models\Tag;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * 72 menus typical of Indonesian cafes: [name, description, price, tags, stock (null = unlimited)].
 * A photo is attached when public/images/menu/<slug>.jpg (or png, webp) exists.
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
                ['Kopi Susu Gula Aren', 'Espresso, susu segar, dan gula aren asli.', 22000, ['Dingin', 'Manis', 'Best Seller'], null],
                ['Kopi Tubruk', 'Kopi hitam seduh tradisional dengan ampas, kuat dan harum.', 12000, ['Panas'], null],
                ['Es Kopi Susu', 'Kopi susu dingin dengan sirup gula cair.', 20000, ['Dingin', 'Manis'], null],
                ['Americano', 'Espresso double dengan air panas, tanpa gula.', 18000, ['Panas'], null],
                ['Cappuccino', 'Espresso dengan susu dan foam tebal.', 24000, ['Panas'], null],
                ['Kopi Vietnam Drip', 'Kopi tetes dengan susu kental manis.', 22000, ['Dingin', 'Manis'], null],
                ['Caffe Latte', 'Espresso dengan susu steam yang lembut.', 25000, ['Panas'], null],
                ['Flat White', 'Espresso pekat dengan microfoam tipis.', 26000, ['Panas'], null],
                ['Espresso', 'Satu shot espresso murni, pekat dan harum.', 15000, ['Panas'], null],
                ['Mochaccino', 'Espresso, cokelat, dan susu dengan whipped cream.', 27000, ['Panas', 'Manis'], null],
                ['Kopi Joss', 'Kopi arang ala angkringan Yogyakarta.', 20000, ['Panas'], null],
                ['Es Kopi Pandan', 'Kopi susu dengan sirup pandan wangi.', 24000, ['Dingin', 'Manis'], null],
            ],
            'Non-Kopi' => [
                ['Es Cokelat', 'Cokelat pekat dengan susu dingin dan es batu.', 22000, ['Dingin', 'Manis'], null],
                ['Matcha Latte', 'Matcha Jepang dengan susu segar.', 25000, ['Dingin'], null],
                ['Wedang Jahe', 'Jahe merah, gula batu, dan serai, disajikan hangat.', 14000, ['Panas', 'Vegetarian'], null],
                ['Es Kelapa Muda', 'Air dan daging kelapa muda dengan sirup.', 16000, ['Dingin', 'Vegetarian'], null],
                ['Es Jeruk Peras', 'Jeruk peras segar tanpa pengawet.', 15000, ['Dingin', 'Vegetarian'], null],
                ['Jus Alpukat', 'Alpukat mentega dengan susu cokelat.', 20000, ['Dingin', 'Manis', 'Best Seller'], null],
                ['Jus Mangga', 'Mangga harum manis blender dengan sedikit susu.', 18000, ['Dingin', 'Manis'], null],
                ['Es Lemon Squash', 'Lemon segar, soda, dan daun mint.', 17000, ['Dingin'], null],
                ['Susu Jahe', 'Susu hangat dengan jahe dan gula aren.', 15000, ['Panas', 'Manis'], null],
                ['Es Markisa', 'Sirup markisa asli dengan es batu dan biji segar.', 16000, ['Dingin', 'Manis'], null],
                ['Red Velvet Latte', 'Susu dengan red velvet, manis dan lembut.', 26000, ['Dingin', 'Manis'], null],
                ['Bandrek', 'Minuman rempah jahe, kayu manis, dan gula merah.', 14000, ['Panas', 'Vegetarian'], null],
            ],
            'Teh' => [
                ['Es Teh Manis', 'Teh melati seduh dengan gula dan es batu.', 8000, ['Dingin', 'Manis'], null],
                ['Teh Tawar Hangat', 'Teh melati seduh tanpa gula.', 6000, ['Panas'], null],
                ['Es Lemon Tea', 'Teh dingin dengan perasan lemon.', 14000, ['Dingin'], null],
                ['Teh Tarik', 'Teh susu yang ditarik hingga berbusa.', 15000, ['Panas', 'Manis'], null],
                ['Thai Tea', 'Teh Thailand dengan susu dan es.', 17000, ['Dingin', 'Manis'], null],
                ['Es Teh Tarik', 'Teh tarik dingin dengan krimer lembut.', 16000, ['Dingin', 'Manis'], null],
                ['Teh Poci Gula Batu', 'Teh tubruk dalam poci tanah liat dengan gula batu.', 12000, ['Panas'], null],
                ['Es Teh Leci', 'Teh dingin dengan buah leci dan sirupnya.', 16000, ['Dingin', 'Manis'], null],
            ],
            'Camilan' => [
                ['Pisang Goreng', 'Pisang kepok goreng renyah dengan taburan keju.', 15000, ['Manis', 'Vegetarian', 'Best Seller'], 30],
                ['Singkong Goreng', 'Singkong goreng dengan sambal bawang.', 13000, ['Pedas', 'Vegetarian'], 40],
                ['Tempe Mendoan', 'Tempe tipis tepung bumbu, setengah matang, dengan cabai rawit.', 14000, ['Pedas', 'Vegetarian'], 35],
                ['Tahu Crispy', 'Tahu goreng tepung dengan bumbu pedas.', 14000, ['Pedas', 'Vegetarian'], 35],
                ['Cireng Rujak', 'Cireng isi dengan sambal rujak pedas manis.', 15000, ['Pedas'], 28],
                ['Kentang Goreng', 'Kentang goreng dengan saus sambal dan mayones.', 16000, ['Vegetarian'], 50],
                ['Roti Bakar Cokelat Keju', 'Roti tebal panggang dengan cokelat dan keju.', 18000, ['Manis', 'Best Seller'], 25],
                ['Risoles Mayo', 'Risoles isi sayur, telur, dan mayones.', 15000, [], 20],
                ['Pisang Bakar Cokelat', 'Pisang bakar dengan lelehan cokelat dan susu.', 17000, ['Manis', 'Vegetarian'], 22],
                ['Bakwan Jagung', 'Bakwan jagung manis goreng gurih dengan cabai rawit.', 13000, ['Pedas', 'Vegetarian'], 30],
                ['Siomay Bandung', 'Siomay ikan dengan tahu, kentang, dan bumbu kacang.', 20000, [], 4],
                ['Lumpia Semarang', 'Lumpia isi rebung dan ayam dengan saus manis.', 16000, [], 26],
                ['Tahu Isi', 'Tahu goreng isi sayuran dengan cabai rawit.', 12000, ['Pedas', 'Vegetarian'], 38],
                ['Onion Ring', 'Bawang bombay goreng tepung renyah dengan saus.', 17000, ['Vegetarian'], 3],
            ],
            'Makanan Berat' => [
                ['Nasi Goreng Kampung', 'Nasi goreng bumbu kampung dengan telur dan kerupuk.', 28000, ['Pedas', 'Best Seller'], 30],
                ['Mie Goreng Jawa', 'Mie goreng bumbu Jawa dengan sayur dan telur.', 26000, [], 30],
                ['Ayam Penyet', 'Ayam goreng dilumat dengan sambal terasi, nasi, dan lalapan.', 30000, ['Pedas'], 25],
                ['Ayam Geprek', 'Ayam crispy digeprek dengan sambal bawang dan nasi.', 28000, ['Pedas'], 25],
                ['Soto Ayam', 'Soto kuning dengan suwiran ayam, telur, dan nasi.', 27000, ['Panas'], 22],
                ['Gado-Gado', 'Sayuran rebus dengan bumbu kacang dan lontong.', 24000, ['Vegetarian'], 20],
                ['Indomie Telur Kornet', 'Mie instan rebus atau goreng dengan telur dan kornet.', 20000, [], 40],
                ['Sate Ayam Madura', 'Sepuluh tusuk sate ayam dengan bumbu kacang dan lontong.', 32000, [], 0],
                ['Nasi Rendang', 'Nasi putih dengan rendang daging sapi, sambal hijau, dan daun singkong.', 34000, ['Pedas', 'Best Seller'], 18],
                ['Nasi Uduk Komplit', 'Nasi gurih santan dengan ayam goreng, tempe orek, dan telur dadar.', 26000, [], 24],
                ['Rawon Daging', 'Sup daging khas Jawa Timur dengan kuah kluwek hitam.', 33000, ['Panas'], 16],
                ['Pecel Lele', 'Lele goreng dengan sambal tomat, lalapan, dan nasi hangat.', 25000, ['Pedas'], 20],
                ['Soto Banjar', 'Soto khas Banjar dengan kuah bening rempah, ayam, dan perkedel.', 28000, ['Panas', 'Best Seller'], 20],
                ['Ketupat Kandangan', 'Ketupat dengan ikan haruan bakar dan kuah santan khas Kandangan.', 30000, [], 12],
                ['Nasi Itik Gambut', 'Nasi dengan itik bumbu habang khas Gambut, Kalimantan Selatan.', 35000, ['Pedas'], 5],
                ['Bubur Ayam', 'Bubur nasi dengan suwiran ayam, cakwe, kacang, dan kerupuk.', 22000, ['Panas'], 25],
            ],
            'Dessert' => [
                ['Es Cendol', 'Cendol, santan, dan gula merah cair.', 15000, ['Dingin', 'Manis', 'Vegetarian'], null],
                ['Es Teler', 'Alpukat, kelapa, dan nangka dengan susu dan sirup.', 18000, ['Dingin', 'Manis', 'Vegetarian'], null],
                ['Klepon Cake', 'Bolu pandan isi gula merah dengan kelapa parut.', 20000, ['Manis', 'Best Seller'], 15],
                ['Martabak Manis Mini', 'Martabak mini cokelat, kacang, dan keju.', 22000, ['Manis', 'Panas'], 18],
                ['Pudding Gula Merah', 'Puding susu dengan saus gula merah.', 14000, ['Dingin', 'Manis'], 20],
                ['Es Campur', 'Aneka buah, cincau, dan roti dengan sirup dan susu.', 17000, ['Dingin', 'Manis'], null],
                ['Kue Lapis Legit', 'Kue lapis tipis kaya rempah dan mentega.', 18000, ['Manis'], 14],
                ['Brownies Cokelat', 'Brownies panggang lembut dengan cokelat leleh.', 20000, ['Manis'], 16],
                ['Es Doger', 'Es serut dengan santan, tape, kacang, dan roti.', 16000, ['Dingin', 'Manis'], null],
                ['Bingka Barandam', 'Bingka khas Banjar yang dipanggang dengan arang, manis dan legit.', 15000, ['Manis', 'Vegetarian'], 12],
            ],
        ];

        foreach ($menus as $categoryName => $items) {
            $category = Category::firstOrCreate(['name' => $categoryName]);

            foreach ($items as [$name, $description, $price, $tagNames, $stock]) {
                $attributes = [
                    'description' => $description,
                    'price' => $price,
                    'stock' => $stock,
                    'is_available' => $name !== 'Sate Ayam Madura',
                ];

                if ($photo = self::photoFor($name)) {
                    $attributes['image'] = $photo;
                }

                $menu = Menu::updateOrCreate(['category_id' => $category->id, 'name' => $name], $attributes);

                $menu->tags()->sync(collect($tagNames)->map(fn (string $tag) => $tags[$tag]->id)->all());
            }
        }
    }

    /**
     * Relative path of the shipped photo of a menu, or null when the file has not been added yet.
     */
    public static function photoFor(string $name): ?string
    {
        $slug = Str::slug($name);

        foreach (['jpg', 'jpeg', 'png', 'webp'] as $extension) {
            if (is_file(public_path("images/menu/{$slug}.{$extension}"))) {
                return "images/menu/{$slug}.{$extension}";
            }
        }

        return null;
    }
}
