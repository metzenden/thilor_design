<?php

namespace App\Console\Commands;

use App\Models\ProductImage;
use App\Support\ImageUploader;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

/**
 * Remplace les visuels de démonstration (motifs générés) par de vraies photos
 * de mode libres de droits (API Pexels), pour une démo plus parlante
 * visuellement. Les requêtes de recherche ciblent le type de vêtement (robe,
 * boubou/kaftan, dashiki, accessoires...) sans restreindre à des photos
 * étiquetées "africaines" — l'objectif est une photo de mode pertinente pour
 * l'article (ex. un mannequin en boubou/caftan), quelle que soit l'origine
 * de la photo.
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
     * Vérifiés DANS CET ORDRE ; le premier qui matche gagne — du plus
     * spécifique (type de vêtement/accessoire précis) au plus générique.
     * "wax" est délibérément en dernier : décrit un imprimé de tissu, présent
     * dans la quasi-totalité des noms de produits du catalogue, et masquerait
     * sinon des mots-clés bien plus pertinents (turban, sac, chemise...) pour
     * la recherche de photo — c'est exactement le bug qui limitait les
     * résultats à une requête générique répétée pour la plupart des produits.
     */
    private const KEYWORD_QUERIES = [
        'turban' => 'headwrap turban fashion model',
        'sac' => 'handbag fashion accessory',
        'boucle' => 'earrings fashion jewelry model',
        'chemise' => 'printed shirt fashion man',
        'veste' => 'printed jacket fashion model',
        'combinaison' => 'jumpsuit fashion model',
        'jupe' => 'printed skirt fashion model',
        'dashiki' => 'printed tunic shirt fashion model',
        'bazin' => 'embroidered dress fashion model',
        'boubou' => 'kaftan dress fashion model',
        'bogolan' => 'printed textile fashion dress',
        'ensemble' => 'fashion outfit model',
        'robe' => 'colorful dress fashion model woman',
        'wax' => 'colorful print dress fashion model',
    ];

    /** Repli par catégorie si aucun mot-clé du nom ne correspond. */
    private const CATEGORY_QUERIES = [
        'Femme' => 'woman fashion model dress',
        'Homme' => 'man fashion model outfit',
        'Enfant' => 'kids fashion model',
        'Haute couture' => 'haute couture fashion dress',
        'Accessoires' => 'fashion accessories',
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
            $photos = $this->searchPexels($apiKey, $query, $perProduct + 15, $usedPhotoIds);

            // Repli : le pool de photos pour cette requête précise est épuisé
            // (plusieurs produits partagent souvent le même mot-clé) — on
            // retente avec la requête plus large de la catégorie avant d'abandonner.
            if (empty($photos)) {
                $fallbackQuery = self::CATEGORY_QUERIES[$product->category->name] ?? 'fashion model';

                if ($fallbackQuery !== $query) {
                    $photos = $this->searchPexels($apiKey, $fallbackQuery, $perProduct + 15, $usedPhotoIds);
                    $query = $fallbackQuery;
                }
            }

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

        return self::CATEGORY_QUERIES[$product->category->name] ?? 'fashion model';
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
                'per_page' => min($perPage, 80), // 80 = maximum autorisé par l'API Pexels
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
