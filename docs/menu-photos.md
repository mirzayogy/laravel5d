# Menu photos

The app shows a photo for every menu when a file named after the menu exists in `public/images/menu/`. Without a photo, a colored placeholder with the category icon is shown.

## How to add the photos

1. Search each keyword below in Google Images (or a free stock site such as Unsplash or Pexels) and pick a clear, landscape photo of the dish.
2. Save it in `public/images/menu/` with the **exact filename** in the table (jpg, png, or webp all work).
3. Run `php artisan menu:link-photos` (no need to reseed). Add `--list` to see which menus still have no photo.

Only use photos you are allowed to use (free licence or your own photos). Admins can also upload a photo per menu from **Manage menu**.

| # | Menu | Category | Price | Filename | Search keyword |
|---|---|---|---|---|---|
| 1 | Kopi Susu Gula Aren | Kopi | Rp 22.000 | `kopi-susu-gula-aren.jpg` | es kopi susu gula aren |
| 2 | Kopi Tubruk | Kopi | Rp 12.000 | `kopi-tubruk.jpg` | kopi tubruk gelas |
| 3 | Es Kopi Susu | Kopi | Rp 20.000 | `es-kopi-susu.jpg` | es kopi susu |
| 4 | Americano | Kopi | Rp 18.000 | `americano.jpg` | americano coffee |
| 5 | Cappuccino | Kopi | Rp 24.000 | `cappuccino.jpg` | cappuccino latte art |
| 6 | Kopi Vietnam Drip | Kopi | Rp 22.000 | `kopi-vietnam-drip.jpg` | vietnam drip coffee |
| 7 | Caffe Latte | Kopi | Rp 25.000 | `caffe-latte.jpg` | caffe latte |
| 8 | Flat White | Kopi | Rp 26.000 | `flat-white.jpg` | flat white coffee |
| 9 | Espresso | Kopi | Rp 15.000 | `espresso.jpg` | espresso shot |
| 10 | Mochaccino | Kopi | Rp 27.000 | `mochaccino.jpg` | mochaccino |
| 11 | Kopi Joss | Kopi | Rp 20.000 | `kopi-joss.jpg` | kopi joss arang |
| 12 | Es Kopi Pandan | Kopi | Rp 24.000 | `es-kopi-pandan.jpg` | es kopi pandan |
| 13 | Es Cokelat | Non-Kopi | Rp 22.000 | `es-cokelat.jpg` | es cokelat |
| 14 | Matcha Latte | Non-Kopi | Rp 25.000 | `matcha-latte.jpg` | matcha latte |
| 15 | Wedang Jahe | Non-Kopi | Rp 14.000 | `wedang-jahe.jpg` | wedang jahe |
| 16 | Es Kelapa Muda | Non-Kopi | Rp 16.000 | `es-kelapa-muda.jpg` | es kelapa muda |
| 17 | Es Jeruk Peras | Non-Kopi | Rp 15.000 | `es-jeruk-peras.jpg` | es jeruk peras |
| 18 | Jus Alpukat | Non-Kopi | Rp 20.000 | `jus-alpukat.jpg` | jus alpukat |
| 19 | Jus Mangga | Non-Kopi | Rp 18.000 | `jus-mangga.jpg` | jus mangga |
| 20 | Es Lemon Squash | Non-Kopi | Rp 17.000 | `es-lemon-squash.jpg` | lemon squash |
| 21 | Susu Jahe | Non-Kopi | Rp 15.000 | `susu-jahe.jpg` | susu jahe |
| 22 | Es Markisa | Non-Kopi | Rp 16.000 | `es-markisa.jpg` | es markisa |
| 23 | Red Velvet Latte | Non-Kopi | Rp 26.000 | `red-velvet-latte.jpg` | red velvet latte |
| 24 | Bandrek | Non-Kopi | Rp 14.000 | `bandrek.jpg` | bandrek |
| 25 | Es Teh Manis | Teh | Rp 8.000 | `es-teh-manis.jpg` | es teh manis |
| 26 | Teh Tawar Hangat | Teh | Rp 6.000 | `teh-tawar-hangat.jpg` | teh tawar hangat |
| 27 | Es Lemon Tea | Teh | Rp 14.000 | `es-lemon-tea.jpg` | es lemon tea |
| 28 | Teh Tarik | Teh | Rp 15.000 | `teh-tarik.jpg` | teh tarik |
| 29 | Thai Tea | Teh | Rp 17.000 | `thai-tea.jpg` | thai tea |
| 30 | Es Teh Tarik | Teh | Rp 16.000 | `es-teh-tarik.jpg` | es teh tarik |
| 31 | Teh Poci Gula Batu | Teh | Rp 12.000 | `teh-poci-gula-batu.jpg` | teh poci gula batu |
| 32 | Es Teh Leci | Teh | Rp 16.000 | `es-teh-leci.jpg` | es teh leci |
| 33 | Pisang Goreng | Camilan | Rp 15.000 | `pisang-goreng.jpg` | pisang goreng keju |
| 34 | Singkong Goreng | Camilan | Rp 13.000 | `singkong-goreng.jpg` | singkong goreng |
| 35 | Tempe Mendoan | Camilan | Rp 14.000 | `tempe-mendoan.jpg` | tempe mendoan |
| 36 | Tahu Crispy | Camilan | Rp 14.000 | `tahu-crispy.jpg` | tahu crispy |
| 37 | Cireng Rujak | Camilan | Rp 15.000 | `cireng-rujak.jpg` | cireng rujak |
| 38 | Kentang Goreng | Camilan | Rp 16.000 | `kentang-goreng.jpg` | kentang goreng |
| 39 | Roti Bakar Cokelat Keju | Camilan | Rp 18.000 | `roti-bakar-cokelat-keju.jpg` | roti bakar cokelat keju |
| 40 | Risoles Mayo | Camilan | Rp 15.000 | `risoles-mayo.jpg` | risoles mayo |
| 41 | Pisang Bakar Cokelat | Camilan | Rp 17.000 | `pisang-bakar-cokelat.jpg` | pisang bakar cokelat |
| 42 | Bakwan Jagung | Camilan | Rp 13.000 | `bakwan-jagung.jpg` | bakwan jagung |
| 43 | Siomay Bandung | Camilan | Rp 20.000 | `siomay-bandung.jpg` | siomay bandung |
| 44 | Lumpia Semarang | Camilan | Rp 16.000 | `lumpia-semarang.jpg` | lumpia semarang |
| 45 | Tahu Isi | Camilan | Rp 12.000 | `tahu-isi.jpg` | tahu isi |
| 46 | Onion Ring | Camilan | Rp 17.000 | `onion-ring.jpg` | onion ring |
| 47 | Nasi Goreng Kampung | Makanan Berat | Rp 28.000 | `nasi-goreng-kampung.jpg` | nasi goreng kampung |
| 48 | Mie Goreng Jawa | Makanan Berat | Rp 26.000 | `mie-goreng-jawa.jpg` | mie goreng jawa |
| 49 | Ayam Penyet | Makanan Berat | Rp 30.000 | `ayam-penyet.jpg` | ayam penyet sambal |
| 50 | Ayam Geprek | Makanan Berat | Rp 28.000 | `ayam-geprek.jpg` | ayam geprek |
| 51 | Soto Ayam | Makanan Berat | Rp 27.000 | `soto-ayam.jpg` | soto ayam |
| 52 | Gado-Gado | Makanan Berat | Rp 24.000 | `gado-gado.jpg` | gado gado |
| 53 | Indomie Telur Kornet | Makanan Berat | Rp 20.000 | `indomie-telur-kornet.jpg` | indomie telur kornet |
| 54 | Sate Ayam Madura | Makanan Berat | Rp 32.000 | `sate-ayam-madura.jpg` | sate ayam madura |
| 55 | Nasi Rendang | Makanan Berat | Rp 34.000 | `nasi-rendang.jpg` | nasi rendang |
| 56 | Nasi Uduk Komplit | Makanan Berat | Rp 26.000 | `nasi-uduk-komplit.jpg` | nasi uduk komplit |
| 57 | Rawon Daging | Makanan Berat | Rp 33.000 | `rawon-daging.jpg` | rawon daging |
| 58 | Pecel Lele | Makanan Berat | Rp 25.000 | `pecel-lele.jpg` | pecel lele |
| 59 | Soto Banjar | Makanan Berat | Rp 28.000 | `soto-banjar.jpg` | soto banjar |
| 60 | Ketupat Kandangan | Makanan Berat | Rp 30.000 | `ketupat-kandangan.jpg` | ketupat kandangan |
| 61 | Nasi Itik Gambut | Makanan Berat | Rp 35.000 | `nasi-itik-gambut.jpg` | nasi itik gambut |
| 62 | Bubur Ayam | Makanan Berat | Rp 22.000 | `bubur-ayam.jpg` | bubur ayam |
| 63 | Es Cendol | Dessert | Rp 15.000 | `es-cendol.jpg` | es cendol |
| 64 | Es Teler | Dessert | Rp 18.000 | `es-teler.jpg` | es teler |
| 65 | Klepon Cake | Dessert | Rp 20.000 | `klepon-cake.jpg` | klepon cake |
| 66 | Martabak Manis Mini | Dessert | Rp 22.000 | `martabak-manis-mini.jpg` | martabak manis mini |
| 67 | Pudding Gula Merah | Dessert | Rp 14.000 | `pudding-gula-merah.jpg` | pudding gula merah |
| 68 | Es Campur | Dessert | Rp 17.000 | `es-campur.jpg` | es campur |
| 69 | Kue Lapis Legit | Dessert | Rp 18.000 | `kue-lapis-legit.jpg` | kue lapis legit |
| 70 | Brownies Cokelat | Dessert | Rp 20.000 | `brownies-cokelat.jpg` | brownies cokelat |
| 71 | Es Doger | Dessert | Rp 16.000 | `es-doger.jpg` | es doger |
| 72 | Bingka Barandam | Dessert | Rp 15.000 | `bingka-barandam.jpg` | bingka barandam |
