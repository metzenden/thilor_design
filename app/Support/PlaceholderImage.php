<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;

/**
 * Génère de petites images de démonstration (fond coloré + libellé) sans dépendance
 * réseau, pour peupler les seeders sans avoir à télécharger de vraies photos produits.
 */
class PlaceholderImage
{
    private const PALETTE = [
        [200, 155, 60],  // doré
        [122, 33, 41],   // bordeaux
        [37, 66, 58],    // vert forêt
        [180, 96, 40],   // terracotta
        [61, 74, 94],    // bleu ardoise
    ];

    public static function make(string $label, int $width = 800, int $height = 1000): string
    {
        [$r, $g, $b] = self::PALETTE[array_rand(self::PALETTE)];

        $image = imagecreatetruecolor($width, $height);
        imagefilledrectangle($image, 0, 0, $width, $height, imagecolorallocate($image, $r, $g, $b));

        // Bande dorée décorative
        $gold = imagecolorallocate($image, 200, 155, 60);
        imagefilledrectangle($image, 0, (int) ($height * 0.85), $width, $height, $gold);

        $white = imagecolorallocate($image, 255, 255, 255);
        $lines = self::wrap($label, 22);
        $y = (int) ($height / 2) - (count($lines) * 20 / 2);

        foreach ($lines as $line) {
            $x = (int) (($width - strlen($line) * 9) / 2);
            imagestring($image, 5, max($x, 10), $y, $line, $white);
            $y += 22;
        }

        ob_start();
        imagejpeg($image, null, 82);
        $contents = ob_get_clean();
        imagedestroy($image);

        return $contents;
    }

    public static function store(string $directory, string $filename, string $label, int $width = 800, int $height = 1000): string
    {
        $path = trim($directory, '/').'/'.$filename;
        Storage::disk('public')->put($path, self::make($label, $width, $height));

        return $path;
    }

    private static function wrap(string $text, int $maxLen): array
    {
        $words = explode(' ', $text);
        $lines = [];
        $current = '';

        foreach ($words as $word) {
            $candidate = trim($current.' '.$word);
            if (strlen($candidate) > $maxLen && $current !== '') {
                $lines[] = $current;
                $current = $word;
            } else {
                $current = $candidate;
            }
        }

        if ($current !== '') {
            $lines[] = $current;
        }

        return $lines;
    }
}
