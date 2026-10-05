<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\CompatibilityType;
use App\Models\Product;
use App\Models\Tag;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $tags = collect(['gaming', 'budget', 'flagship', 'overclocking', 'rgb', 'silent', 'workstation'])
            ->map(fn ($name) => Tag::firstOrCreate(['name' => $name]));

        foreach (Category::with('specificationTypes')->get() as $category) {
            Product::factory()
                ->count(4)
                ->create(['category_id' => $category->id])
                ->each(function (Product $product) use ($category, $tags) {
                    $product->images()->createMany([
                        ['path' => "products/{$product->slug}-1.jpg", 'is_primary' => true, 'sort_order' => 1],
                        ['path' => "products/{$product->slug}-2.jpg", 'is_primary' => false, 'sort_order' => 2],
                    ]);

                    $product->tags()->attach($tags->random(2)->pluck('id')->all());

                    foreach ($category->specificationTypes as $type) {
                        $product->specificationTypes()->attach($type->id, [
                            'value' => $this->valueFor($category->slug, $type->slug),
                        ]);
                    }
                });
        }

        $this->seedCompatibilities();
    }

    private function valueFor(string $categorySlug, string $specSlug): string
    {
        $options = [
            'processor.socket' => ['AM5', 'AM4', 'LGA1700'],
            'processor.cores' => [4, 6, 8, 12, 16],
            'processor.threads' => [8, 12, 16, 24, 32],
            'processor.base-clock' => [3.2, 3.8, 4.5, 5.0],
            'processor.tdp' => [65, 105, 125],
            'motherboard.socket' => ['AM5', 'AM4', 'LGA1700'],
            'motherboard.chipset' => ['B650', 'X670', 'B760', 'Z790'],
            'motherboard.ram-type' => ['DDR4', 'DDR5'],
            'motherboard.ram-slots' => [2, 4],
            'motherboard.form-factor' => ['ATX', 'Micro-ATX', 'Mini-ITX'],
            'motherboard.pcie-version' => ['4.0', '5.0'],
            'memory.type' => ['DDR4', 'DDR5'],
            'memory.capacity' => [8, 16, 32, 64],
            'memory.speed' => [3200, 3600, 5200, 6000],
            'graphics-card.vram' => [8, 12, 16, 24],
            'graphics-card.length' => [240, 285, 320, 350],
            'graphics-card.tdp' => [150, 200, 285, 320],
            'graphics-card.power-connector' => ['8-pin', '2x 8-pin', '12VHPWR'],
            'storage.interface' => ['SATA', 'NVMe'],
            'storage.capacity' => [256, 512, 1000, 2000],
            'storage.form-factor' => ['2.5"', 'M.2 2280'],
            'power-supply.wattage' => [550, 650, 750, 850],
            'power-supply.efficiency-rating' => ['80+ Bronze', '80+ Gold'],
            'power-supply.modular' => ['true', 'false'],
            'casing.supported-form-factor' => ['ATX, Micro-ATX, Mini-ITX', 'Micro-ATX, Mini-ITX'],
            'casing.max-gpu-length' => [300, 340, 380, 420],
            'cpu-cooler.supported-sockets' => ['AM5, AM4, LGA1700', 'AM4', 'LGA1700'],
            'cpu-cooler.tdp-rating' => [150, 200, 250],
            'cpu-cooler.height' => [140, 155, 165],
        ];

        $choices = $options["{$categorySlug}.{$specSlug}"] ?? ['N/A'];

        return (string) $choices[array_rand($choices)];
    }

    private function seedCompatibilities(): void
    {
        $type = CompatibilityType::where('slug', 'cpu-motherboard-socket')->firstOrFail();

        $cpus = Product::whereHas('category', fn ($q) => $q->where('slug', 'processor'))->get();
        $boards = Product::whereHas('category', fn ($q) => $q->where('slug', 'motherboard'))->get();

        foreach ($cpus as $cpu) {
            $cpuSocket = $this->specValue($cpu, 'socket');

            foreach ($boards as $board) {
                $boardSocket = $this->specValue($board, 'socket');

                $cpu->compatibleProducts()->attach($board->id, [
                    'compatibility_type_id' => $type->id,
                    'status' => $cpuSocket === $boardSocket ? 'compatible' : 'incompatible',
                    'note' => "Socket CPU {$cpuSocket} vs socket motherboard {$boardSocket}",
                ]);
            }
        }
    }

    private function specValue(Product $product, string $slug): ?string
    {
        return $product->specificationTypes()
            ->where('specification_types.slug', $slug)
            ->first()?->pivot->value;
    }
}