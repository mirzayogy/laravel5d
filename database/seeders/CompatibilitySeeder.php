<?php

namespace Database\Seeders;

use App\Models\CompatibilityRule;
use App\Models\CompatibilityType;
use App\Models\SpecificationType;
use Illuminate\Database\Seeder;

class CompatibilitySeeder extends Seeder
{
    public function run(): void
    {
        // [nama, slug, kategori sumber, spec sumber, kategori target, spec target, operator]
        $rules = [
            ['CPU – Motherboard Socket', 'cpu-motherboard-socket', 'processor', 'socket', 'motherboard', 'socket', 'equals'],
            ['RAM – Motherboard Type', 'ram-motherboard-type', 'memory', 'type', 'motherboard', 'ram-type', 'equals'],
            ['Motherboard – Casing Form Factor', 'motherboard-casing-form-factor', 'casing', 'supported-form-factor', 'motherboard', 'form-factor', 'contains'],
            ['GPU – Casing Length', 'gpu-casing-length', 'casing', 'max-gpu-length', 'graphics-card', 'length', 'gte'],
            ['CPU Cooler – CPU Socket', 'cooler-cpu-socket', 'cpu-cooler', 'supported-sockets', 'processor', 'socket', 'contains'],
            ['PSU – Power Requirement', 'psu-power-requirement', 'power-supply', 'wattage', 'graphics-card', 'tdp', 'gte'],
        ];

        foreach ($rules as [$name, $slug, $srcCat, $srcSpec, $tgtCat, $tgtSpec, $operator]) {
            $type = CompatibilityType::updateOrCreate(
                ['slug' => $slug],
                ['name' => $name, 'description' => "Aturan kecocokan: {$name}"]
            );

            $source = $this->spec($srcCat, $srcSpec);
            $target = $this->spec($tgtCat, $tgtSpec);

            CompatibilityRule::updateOrCreate(
                ['compatibility_type_id' => $type->id],
                [
                    'source_category_id' => $source->category_id,
                    'target_category_id' => $target->category_id,
                    'source_spec_type_id' => $source->id,
                    'target_spec_type_id' => $target->id,
                    'operator' => $operator,
                    'description' => "{$srcCat}.{$srcSpec} {$operator} {$tgtCat}.{$tgtSpec}",
                ]
            );
        }
    }

    private function spec(string $categorySlug, string $specSlug): SpecificationType
    {
        return SpecificationType::whereHas('category', fn ($q) => $q->where('slug', $categorySlug))
            ->where('slug', $specSlug)
            ->firstOrFail();
    }
}