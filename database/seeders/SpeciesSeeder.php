<?php

namespace Database\Seeders;

use App\Models\Species;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SpeciesSeeder extends Seeder
{
    /**
     * Seed the species that are commonly entered in songbird contests.
     */
    public function run(): void
    {
        $species = [
            ['Murai Batu', 'Copsychus malabaricus', 'Long tail and a long, varied song. A staple of large contests.'],
            ['Kacer', 'Copsychus saularis', 'Oriental magpie-robin, valued for stamina and a fast song.'],
            ['Cucak Hijau', 'Chloropsis sonnerati', 'Greater green leafbird, known for its loud and mimicking song.'],
            ['Cendet', 'Lanius schach', 'Long-tailed shrike, scored on volume and call variety.'],
            ['Lovebird', 'Agapornis spp.', 'Small parrot, judged on the length of its sustained call.'],
            ['Kenari', 'Serinus canaria', 'Domestic canary, judged on clarity and song structure.'],
            ['Branjangan', 'Mirafra javanica', 'Horsfield\'s bush lark, known for its long aerial song.'],
            ['Pleci', 'Zosterops palpebrosus', 'Indian white-eye, small and judged on song quality.'],
        ];

        foreach ($species as [$name, $scientificName, $description]) {
            Species::updateOrCreate(
                ['slug' => Str::slug($name)],
                [
                    'name' => $name,
                    'scientific_name' => $scientificName,
                    'description' => $description,
                ],
            );
        }
    }
}
