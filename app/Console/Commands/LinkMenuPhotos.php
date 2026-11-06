<?php

namespace App\Console\Commands;

use App\Models\Menu;
use Database\Seeders\IndonesianMenuSeeder;
use Illuminate\Console\Command;

class LinkMenuPhotos extends Command
{
    protected $signature = 'menu:link-photos {--list : Only list the menus that have no photo}';

    protected $description = 'Attach the photos in public/images/menu (named after the menu) to their menus';

    public function handle(): int
    {
        $linked = 0;
        $missing = [];

        foreach (Menu::orderBy('name')->get() as $menu) {
            $photo = IndonesianMenuSeeder::photoFor($menu->name);

            if ($photo === null) {
                if (! $menu->image) {
                    $missing[] = $menu->name;
                }

                continue;
            }

            if (! $this->option('list') && $menu->image !== $photo) {
                $menu->update(['image' => $photo]);
                $linked++;
            }
        }

        if (! $this->option('list')) {
            $this->info("Linked {$linked} photo(s).");
        }

        if ($missing !== []) {
            $this->warn(count($missing).' menu(s) still have no photo:');
            $this->line('  '.implode("\n  ", $missing));
        } else {
            $this->info('Every menu has a photo.');
        }

        return self::SUCCESS;
    }
}
