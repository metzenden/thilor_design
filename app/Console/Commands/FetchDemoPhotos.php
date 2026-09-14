<?php

namespace App\Console\Commands;

use App\Models\ProductImage;
use App\Support\ImageUploader;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

/**
 * Remplace les visuels de démonstration (motifs générés) par de vraies photos
 * libres de droits (API Pexels), pour une démo plus parlante visuellement.
 *
 * IMPORTANT — à lancer sur votre machine (avec un accès internet normal),
 * PAS dans un environnement sans accès réseau externe. Les photos obtenues
 * sont des photos de stock à usage libre (licence Pexels : utilisation
 * commerciale autorisée, aucune attribution requise) — ce sont malgré tout
 * des mannequins/vêtements réels qui ne correspondent pas exactement à vos
 * futurs articles. Elles servent uniquement à une démonstration visuelle
 * plus réaliste ; remplacez-les par vos vraies photos produits avant la mise
 * en production (voir docs/GUIDE_ADMINISTRATEUR.md).
 *
 * Préparation :
 *   1. Créez un compte gratuit sur https://www.pexels.com/api/ (approbation
 *      instantanée) et récupérez votre clé API.
 *   2. Lancez : php artisan demo:fetch-photos --key=VOTRE_CLE_API
 *      (ou définissez PEXELS_API_KEY dans votre .env et omettez --key)
 */
class FetchDemoPhotos extends Command
{
    protected $signature = 'demo:fetch-photos
        {--key= : Clé API Pexels (ou définissez PEXELS_API_KEY dans .env)}
        {--per-product=2 : Nombre de photos à récupérer par produit}';

    protected $description = "Remplace les visuels de démonstration générés par de vraies photos libres de droits (Pexels) — à lancer avec un accès internet.";

    /**
     * Mots-clés détectés dans le nom du produit -> requête de recherche Pexels.
     * Vérifiés dans l'ordre ; le premier qui matche gagne.
     */
    private const KEYWORD_QUERIES = [
        'wax' => 'ankara african print dress',
        'bazin' => 'bazin riche african dress',
        'boubou' => 'boubou african traditional dress',
        'dashiki' => 'dashiki african print shirt',
        'bogolan' => 'bogolan african textile fashion',
        'turban' => 'african headwrap turban fashion',
        'sac' => 'african print handbag',
        'boucle' => 'african beaded earrings jewelry',
        'chemise' => 'african print shirt men',
        'veste' => 'african print jacket fashion',
        'combinaison' => 'african print jumpsuit',
        'jupe' => 'african print skirt',
        'robe' => 'african print dress woman',
        'ensemble' => 'african fashion outfit',
    ];

    /** Repli par catégorie si aucun mot-clé du nom ne correspond. */
    private const CATEGORY_QUERIES = [
        'Femme' => 'african fashion woman dress',
        'Homme' => 'african fashion man traditional',
        'Enfant' => 'african fashion kids',
        'Haute couture' => 'african haute couture dress',
        'Accessoires' => 'african fashion accessories',
    ];

    public function handle(): int
    {
        $apiKey = $this->option('key') ?: config('services.pexels.key');

        if (! $apiKey) {
            $this->error('Aucune clé API Pexels fournie. Utilisez --key=VOTRE_CLE ou définissez PEXELS_API_KEY dans .env.');
            $this->line('Créez une clé gratuite sur https://www.pexels.com/api/');

            return self::FAILURE;
        }

        $perProduct = max(1, (int) $this->option('per-product'));
        $products = \App\Models\Product::with(['category', 'images'])->get();

        if ($products->isEmpty()) {
            $this->warn('Aucun produit en base — lancez d\'abord `php artisan migrate --seed`.');

            return self::FAILURE;
        }

        $usedPhotoIds = [];
        $bar = $this->output->createProgressBar($products->count());
        $bar->start();

        foreach ($products as $product) {
            $query = $this->resolveQuery($product);

            $photos = $this->searchPexels($apiKey, $query, $perProduct + 3, $usedPhotoIds);

            if (empty($photos)) {
                $this->newLine();
                $this->warn("Aucune photo trouvée pour « {$product->name} » (requête : {$query}) — conservé tel quel.");
                $bar->advance();

                continue;
            }

            // Supprime les anciens visuels (placeholders générés) avant d'ajouter les vraies photos.
            foreach ($product->images as $oldImage) {
                $oldImage->delete(); // déclenche le nettoyage du fichier (voir CleansUpImageFiles)
            }

            foreach (array_slice($photos, 0, $perProduct) as $index => $photo) {
                $contents = Http::timeout(20)->get($photo['src'])->body();
                $path = ImageUploader::storeFromContents($contents, 'products');

                ProductImage::create([
                    'product_id' => $product->id,
                    'path' => $path,
                    'alt_text' => $product->name,
                    'is_primary' => $index === 0,
                    'position' => $index,
                ]);

                $usedPhotoIds[] = $photo['id'];
                usleep(300_000); // ménage l'API Pexels (limite ~200 req/h)
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        $this->info('Terminé. Rechargez le site pour voir les nouvelles photos.');
        $this->comment('Rappel : ce sont des photos de démonstration (Pexels, libres de droits) — à remplacer par vos vraies photos produits avant la mise en production.');

        return self::SUCCESS;
    }

    private function resolveQuery(\App\Models\Product $product): string
    {
        $name = mb_strtolower($product->name);

        foreach (self::KEYWORD_QUERIES as $keyword => $query) {
            if (str_contains($name, $keyword)) {
                return $query;
            }
        }

        return self::CATEGORY_QUERIES[$product->category->name] ?? 'african fashion';
    }

    /**
     * @return array<int, array{id: int, src: string}>
     */
    private function searchPexels(string $apiKey, string $query, int $perPage, array $excludeIds): array
    {
        $response = Http::withHeaders(['Authorization' => $apiKey])
            ->timeout(15)
            ->get('https://api.pexels.com/v1/search', [
                'query' => $query,
                'per_page' => min($perPage, 15),
                'orientation' => 'portrait',
            ]);

        if (! $response->successful()) {
            return [];
        }

        return collect($response->json('photos', []))
            ->reject(fn ($photo) => in_array($photo['id'], $excludeIds))
            ->map(fn ($photo) => ['id' => $photo['id'], 'src' => $photo['src']['large']])
            ->values()
            ->all();
    }
}
