<?php

namespace App\Support;

use Composer\InstalledVersions;
use DiceBear\Avatar;
use DiceBear\Style;
use Illuminate\Support\Facades\Cache;

/**
 * Profile pictures: the DiceBear "Waves" style (CC0), generated on our own
 * server from a stable seed. The same seed always gives the same avatar, and the
 * waves drift slowly (CSS inside the SVG, which respects prefers-reduced-motion).
 */
class Avatars
{
    private static ?Style $style = null;

    public static function svg(string $seed): string
    {
        $config = config('habitude.avatar');

        return Cache::rememberForever('avatar:waves:v1:'.md5($seed.json_encode($config)), function () use ($seed, $config) {
            self::$style ??= Style::fromJson(file_get_contents(
                InstalledVersions::getInstallPath('dicebear/styles').'/src/waves.json'
            ));

            $svg = (string) new Avatar(self::$style, [
                'seed' => $seed,
                'animationVariant' => $config['animation'],
                'backgroundColor' => $config['backgrounds'],
                'waveColor' => [$config['wave']],
            ]);

            // Drop the generator comment and the licence metadata (CC0, no attribution required) to keep it small.
            return preg_replace(['#<!--.*?-->#s', '#<metadata.*?</metadata>#s'], '', $svg);
        });
    }
}
