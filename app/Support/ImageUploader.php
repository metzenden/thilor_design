<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

/**
 * Traitement des images uploadées (produits, catégories, bannières, pages) :
 * redimensionnement, compression et conversion WebP, avec un nom de fichier
 * généré (jamais le nom d'origine) pour éviter tout risque lié à l'upload.
 * Utilisé comme callback `saveUploadedFileUsing` des champs FileUpload Filament.
 */
class ImageUploader
{
    public static function store(UploadedFile $file, string $directory, int $maxWidth = 1600, int $quality = 80): string
    {
        $manager = new ImageManager(new Driver);

        $image = $manager->read($file->getRealPath());

        if ($image->width() > $maxWidth) {
            $image->scaleDown(width: $maxWidth);
        }

        $encoded = $image->toWebp($quality);

        $filename = trim($directory, '/').'/'.now()->format('Y/m').'/'.Str::uuid().'.webp';

        Storage::disk('public')->put($filename, (string) $encoded);

        return $filename;
    }

    public static function delete(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
