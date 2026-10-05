<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\SpecificationType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SpecificationTypeSeeder extends Seeder
{
    public function run(): void
    {
        // [nama, data_type, unit]
        $specs = [
            'processor' => [
                ['Socket', 'string', null], ['Cores', 'number', null], ['Threads', 'number', null],
                ['Base Clock', 'number', 'GHz'], ['TDP', 'number', 'W'],
            ],
            'motherboard' => [
                ['Socket', 'string', null], ['Chipset', 'string', null], ['RAM Type', 'string', null],
                ['RAM Slots', 'number', null], ['Form Factor', 'string', null], ['PCIe Version', 'string', null],
            ],
            'memory' => [
                ['Type', 'string', null], ['Capacity', 'number', 'GB'], ['Speed', 'number', 'MHz'],
            ],
            'graphics-card' => [
                ['VRAM', 'number', 'GB'], ['Length', 'number', 'mm'], ['TDP', 'number', 'W'],
                ['Power Connector', 'string', null],
            ],
            'storage' => [
                ['Interface', 'string', null], ['Capacity', 'number', 'GB'], ['Form Factor', 'string', null],
            ],
            'power-supply' => [
                ['Wattage', 'number', 'W'], ['Efficiency Rating', 'string', null], ['Modular', 'boolean', null],
            ],
            'casing' => [
                ['Supported Form Factor', 'string', null], ['Max GPU Length', 'number', 'mm'],
            ],
            'cpu-cooler' => [
                ['Supported Sockets', 'string', null], ['TDP Rating', 'number', 'W'], ['Height', 'number', 'mm'],
            ],
        ];

        foreach ($specs as $categorySlug => $types) {
            $category = Category::where('slug', $categorySlug)->firstOrFail();

            foreach ($types as [$name, $dataType, $unit]) {
                SpecificationType::updateOrCreate(
                    ['category_id' => $category->id, 'slug' => Str::slug($name)],
                    ['name' => $name, 'unit' => $unit, 'data_type' => $dataType, 'is_filterable' => true]
                );
            }
        }
    }
}