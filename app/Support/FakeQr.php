<?php

namespace App\Support;

/**
 * Draws a QR-looking SVG for the simulated QRIS payment.
 * It is only a picture made from the seed: it contains no real payment data.
 */
class FakeQr
{
    public static function svg(string $seed, int $size = 25): string
    {
        $finder = fn (int $row, int $col): bool => ($row < 7 && $col < 7)
            || ($row < 7 && $col >= $size - 7)
            || ($row >= $size - 7 && $col < 7);

        $path = '';

        for ($row = 0; $row < $size; $row++) {
            for ($col = 0; $col < $size; $col++) {
                if ($finder($row, $col)) {
                    $localRow = $row < 7 ? $row : $row - ($size - 7);
                    $localCol = $col < 7 ? $col : $col - ($size - 7);
                    $edge = $localRow === 0 || $localRow === 6 || $localCol === 0 || $localCol === 6;
                    $center = $localRow >= 2 && $localRow <= 4 && $localCol >= 2 && $localCol <= 4;
                    $on = $edge || $center;
                } elseif (($row < 8 && $col < 8) || ($row < 8 && $col >= $size - 8) || ($row >= $size - 8 && $col < 8)) {
                    $on = false;
                } else {
                    $on = (hexdec(substr(md5($seed.'|'.$row.'|'.$col), 0, 2)) & 1) === 1;
                }

                if ($on) {
                    $path .= "M{$col} {$row}h1v1h-1z";
                }
            }
        }

        return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="-1 -1 '.($size + 2).' '.($size + 2).'" shape-rendering="crispEdges" role="img" aria-label="QRIS"><rect x="-1" y="-1" width="'.($size + 2).'" height="'.($size + 2).'" fill="#fff"/><path d="'.$path.'" fill="#1c1917"/></svg>';
    }
}
