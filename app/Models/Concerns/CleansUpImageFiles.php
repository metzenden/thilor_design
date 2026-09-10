<?php

namespace App\Models\Concerns;

use App\Support\ImageUploader;

/**
 * Supprime le fichier physique d'un champ image quand il est remplacé ou que
 * le modèle est supprimé, pour éviter les fichiers orphelins dans le stockage.
 * Le modèle doit définir la constante CLEANABLE_IMAGE_FIELDS (liste de noms
 * de colonnes contenant un chemin de fichier sur le disque "public").
 */
trait CleansUpImageFiles
{
    protected static function bootCleansUpImageFiles(): void
    {
        static::deleting(function ($model) {
            foreach (static::CLEANABLE_IMAGE_FIELDS as $field) {
                ImageUploader::delete($model->{$field});
            }
        });

        static::updating(function ($model) {
            foreach (static::CLEANABLE_IMAGE_FIELDS as $field) {
                if ($model->isDirty($field) && $model->getOriginal($field)) {
                    ImageUploader::delete($model->getOriginal($field));
                }
            }
        });
    }
}
