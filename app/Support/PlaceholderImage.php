<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;

/**
 * Génère des visuels de démonstration inspirés des imprimés wax (motifs
 * géométriques répétés + palette de la marque), sans dépendance réseau, pour
 * peupler les seeders en l'absence de vraies photos produits. Ce sont des
 * illustrations de substitution, jamais des photos de produits réels — à
 * remplacer par le vrai catalogue avant mise en production (voir README).
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
        // Déterministe par libellé : le même produit garde le même visuel
        // d'une exécution de seeder à l'autre.
        mt_srand(crc32($label));

        $palette = self::PALETTE;
        shuffle($palette);
        [$baseColor, $accentA, $accentB] = $palette;

        $image = imagecreatetruecolor($width, $height);
        imagealphablending($image, true);
        imagesavealpha($image, true);

        [$r, $g, $b] = $baseColor;
        imagefilledrectangle($image, 0, 0, $width, $height, imagecolorallocate($image, $r, $g, $b));

        self::drawWaxPattern($image, $width, $height, $accentA, $accentB);
        self::drawFrame($image, $width, $height);
        self::drawLabelPanel($image, $width, $height, $label);

        ob_start();
        imagejpeg($image, null, 85);
        $contents = ob_get_clean();
        imagedestroy($image);

        mt_srand(); // ne pas figer l'aléatoire du reste de l'application

        return $contents;
    }

    private static function drawWaxPattern($image, int $width, int $height, array $accentA, array $accentB): void
    {
        $colorA = imagecolorallocatealpha($image, $accentA[0], $accentA[1], $accentA[2], 45);
        $colorB = imagecolorallocatealpha($image, $accentB[0], $accentB[1], $accentB[2], 55);
        $step = (int) round(min($width, $height) / 7);

        for ($row = -1; $row * $step < $height + $step; $row++) {
            $offset = ($row % 2 === 0) ? 0 : (int) ($step / 2);

            for ($col = -1; $col * $step < $width + $step; $col++) {
                $cx = $col * $step + $offset;
                $cy = $row * $step;
                $radius = (int) ($step * 0.32);

                // Motif principal : cercle concentrique façon bouton de pagne wax.
                imagefilledellipse($image, $cx, $cy, $radius * 2, $radius * 2, $colorA);
                imagefilledellipse($image, $cx, $cy, (int) ($radius * 0.9), (int) ($radius * 0.9), $colorB);

                // Losange complémentaire entre les cercles pour un effet tissé.
                $dx = (int) ($step * 0.35);
                $points = [$cx + $step / 2, $cy - $dx, $cx + $step / 2 + $dx, $cy, $cx + $step / 2, $cy + $dx, $cx + $step / 2 - $dx, $cy];
                imagefilledpolygon($image, $points, $colorB);
            }
        }
    }

    private static function drawFrame($image, int $width, int $height): void
    {
        $gold = imagecolorallocate($image, 200, 155, 60);
        $thickness = max(4, (int) ($width * 0.012));

        for ($i = 0; $i < $thickness; $i++) {
            imagerectangle($image, $i, $i, $width - 1 - $i, $height - 1 - $i, $gold);
        }
    }

    private static function drawLabelPanel($image, int $width, int $height, string $label): void
    {
        $panelHeight = (int) ($height * 0.22);
        $panelTop = $height - $panelHeight;

        // Voile sombre dégradé pour que le libellé reste lisible sur le motif.
        for ($y = $panelTop; $y < $height; $y++) {
            $ratio = ($y - $panelTop) / max($panelHeight, 1);
            $alpha = (int) (90 - $ratio * 40); // de ~65% à ~85% d'opacité
            $overlay = imagecolorallocatealpha($image, 20, 16, 12, max(0, min(127, $alpha)));
            imageline($image, 0, $y, $width, $y, $overlay);
        }

        $white = imagecolorallocate($image, 255, 255, 255);
        $lines = self::wrap($label, 22);
        $lineHeight = 22;
        $y = $panelTop + (int) (($panelHeight - count($lines) * $lineHeight) / 2);

        foreach ($lines as $line) {
            $x = (int) (($width - strlen($line) * 9) / 2);
            imagestring($image, 5, max($x, 10), $y, $line, $white);
            $y += $lineHeight;
        }
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
