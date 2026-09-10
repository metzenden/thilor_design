<?php

namespace Database\Seeders;

use App\Models\Banner;
use App\Models\Category;
use App\Models\Collection;
use App\Models\Coupon;
use App\Models\NewsletterSubscriber;
use App\Models\Page;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\Review;
use App\Models\Setting;
use App\Models\ShippingMethod;
use App\Models\User;
use App\Support\PlaceholderImage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ShopSeeder extends Seeder
{
    /** @var array<string, Category> */
    private array $categories = [];

    public function run(): void
    {
        $this->seedSettings();
        $this->seedCategories();
        $this->seedCollections();
        $this->seedShippingMethods();
        $this->seedCoupons();
        $this->seedBanners();
        $this->seedPages();
        $this->seedProducts();
        $this->seedNewsletter();
    }

    private function seedSettings(): void
    {
        Setting::set('shop_name', 'THILOR DESIGN');
        Setting::set('shop_tagline', "L'élégance africaine à votre style");
        Setting::set('shop_phone', '+221 77 123 45 67');
        Setting::set('shop_email', 'contact@thilor-design.com');
        Setting::set('shop_address', 'Rue 10, Liberté 6 Extension, Dakar, Sénégal');
        Setting::set('social_facebook', 'https://facebook.com/thilordesign');
        Setting::set('social_instagram', 'https://instagram.com/thilordesign');
        Setting::set('social_tiktok', 'https://tiktok.com/@thilordesign');
        Setting::set('social_youtube', 'https://youtube.com/@thilordesign');
        Setting::set('seo_default_title', 'THILOR DESIGN — Tenues africaines élégantes');
        Setting::set('seo_default_description', "Boutique en ligne de tenues africaines : Wax, Bazin, Bogolan. Livraison au Sénégal. Paiement à la livraison disponible.");
    }

    private function seedCategories(): void
    {
        $names = ['Femme', 'Homme', 'Enfant', 'Haute couture', 'Accessoires'];

        foreach ($names as $position => $name) {
            $this->categories[$name] = Category::updateOrCreate(
                ['slug' => Str::slug($name)],
                [
                    'name' => $name,
                    'description' => "Découvrez notre sélection {$name}.",
                    'image' => PlaceholderImage::store('categories', Str::slug($name).'.jpg', $name, 800, 600),
                    'position' => $position,
                    'is_active' => true,
                    'meta_title' => "{$name} — THILOR DESIGN",
                    'meta_description' => "Tenues africaines {$name} chez THILOR DESIGN, l'élégance africaine à votre style.",
                ]
            );
        }
    }

    private function seedCollections(): void
    {
        foreach (['Prêt-à-porter', 'Nouveautés Wax 2026', 'Édition Mariage'] as $position => $name) {
            Collection::updateOrCreate(
                ['slug' => Str::slug($name)],
                [
                    'name' => $name,
                    'description' => "Collection {$name} — pièces sélectionnées par nos stylistes.",
                    'image' => PlaceholderImage::store('collections', Str::slug($name).'.jpg', $name, 1200, 500),
                    'position' => $position,
                    'is_active' => true,
                ]
            );
        }
    }

    private function seedShippingMethods(): void
    {
        $methods = [
            ['name' => 'Livraison à domicile', 'cost' => 2000, 'delay_label' => '2 à 3 jours ouvrés'],
            ['name' => 'Point relais', 'cost' => 1000, 'delay_label' => '3 à 5 jours ouvrés'],
            ['name' => 'Livraison express Dakar', 'cost' => 3500, 'delay_label' => '24h à 72h'],
        ];

        foreach ($methods as $position => $method) {
            ShippingMethod::updateOrCreate(
                ['name' => $method['name']],
                [...$method, 'is_active' => true, 'position' => $position]
            );
        }
    }

    private function seedCoupons(): void
    {
        Coupon::updateOrCreate(['code' => 'BIENVENUE10'], [
            'type' => 'percent', 'value' => 10, 'min_amount' => 10000,
            'starts_at' => now()->subMonth(), 'ends_at' => now()->addMonths(6),
            'usage_limit' => 500, 'is_active' => true,
        ]);

        Coupon::updateOrCreate(['code' => 'THILOR5000'], [
            'type' => 'fixed', 'value' => 5000, 'min_amount' => 30000,
            'starts_at' => now()->subMonth(), 'ends_at' => now()->addMonths(3),
            'usage_limit' => 200, 'is_active' => true,
        ]);
    }

    private function seedBanners(): void
    {
        Banner::updateOrCreate(['title' => "L'élégance africaine à votre style"], [
            'subtitle' => "Découvrez nos créations uniques qui célèbrent la richesse et la beauté de l'Afrique.",
            'image' => PlaceholderImage::store('banners', 'hero.jpg', 'THILOR DESIGN', 1600, 900),
            'cta_label' => 'Découvrir la collection',
            'link' => '/catalogue',
            'position_key' => 'home_hero',
            'position' => 0,
            'is_active' => true,
        ]);

        Banner::updateOrCreate(['title' => 'Promotions de saison'], [
            'subtitle' => "Jusqu'à -30% sur une sélection de robes en Wax.",
            'image' => PlaceholderImage::store('banners', 'promo.jpg', 'PROMOTIONS', 1600, 700),
            'cta_label' => 'Voir les promotions',
            'link' => '/promotions',
            'position_key' => 'home_secondary',
            'position' => 1,
            'is_active' => true,
        ]);
    }

    private function seedPages(): void
    {
        $staticPages = [
            'À propos' => "THILOR DESIGN est une maison de mode sénégalaise dédiée à l'élégance africaine.",
            'Livraison' => "Nous livrons partout au Sénégal sous 24h à 5 jours selon le mode choisi.",
            'Retours & remboursements' => "Vous disposez de 14 jours pour changer d'avis sur un article non porté.",
            'Conditions générales' => "Conditions générales de vente applicables à toute commande passée sur THILOR DESIGN.",
            'Politique de confidentialité' => "Vos données personnelles sont protégées et ne sont jamais revendues.",
            'FAQ' => "Retrouvez les réponses aux questions les plus fréquentes sur nos produits, la livraison et le paiement.",
            'Guide des tailles' => "Consultez notre guide des tailles pour choisir la coupe idéale.",
        ];

        foreach ($staticPages as $title => $intro) {
            Page::updateOrCreate(['slug' => Str::slug($title)], [
                'type' => 'page',
                'title' => $title,
                'excerpt' => $intro,
                'content' => '<p>'.$intro.'</p>',
                'is_published' => true,
                'published_at' => now()->subMonths(2),
            ]);
        }

        Page::factory()->count(4)->create(['type' => 'article']);
    }

    private function seedProducts(): void
    {
        $catalogue = [
            'Femme' => ['Robe en Wax Ankara', 'Ensemble 2 pièces Wax', 'Robe Longue Wax', 'Robe Boubou Chic', 'Combinaison Africaine', 'Jupe et Top Wax'],
            'Homme' => ['Boubou Royal Homme', 'Chemise Wax Homme', 'Ensemble Dashiki', 'Veste Bazin Brodée'],
            'Enfant' => ['Robe Wax Fillette', 'Ensemble Garçon Wax', 'Robe Cérémonie Enfant'],
            'Haute couture' => ['Robe de Soirée Haute Couture', 'Tenue Mariage Bazin Riche', 'Ensemble Cérémonie Brodé'],
            'Accessoires' => ['Sac à Main Wax', 'Turban Wax Assorti', 'Boucles d\'Oreilles Africaines'],
        ];

        foreach ($catalogue as $categoryName => $productNames) {
            $category = $this->categories[$categoryName];

            foreach ($productNames as $name) {
                /** @var Product $product */
                $product = Product::factory()->create([
                    'category_id' => $category->id,
                    'name' => $name,
                    'slug' => Str::slug($name).'-'.Str::lower(Str::random(5)),
                ]);

                foreach (range(1, 3) as $i) {
                    ProductImage::create([
                        'product_id' => $product->id,
                        'path' => PlaceholderImage::store('products', "{$product->slug}-{$i}.jpg", $name),
                        'alt_text' => "{$name} — photo {$i}",
                        'is_primary' => $i === 1,
                        'position' => $i - 1,
                    ]);
                }

                $sizes = ['S', 'M', 'L', 'XL'];
                $colors = [['Bleu', '#2E4A7D'], ['Bordeaux', '#7A2129'], ['Doré', '#C89B3C']];

                foreach ($colors as [$color, $hex]) {
                    foreach ($sizes as $size) {
                        ProductVariant::create([
                            'product_id' => $product->id,
                            'size' => $size,
                            'color' => $color,
                            'color_hex' => $hex,
                            'sku' => strtoupper('TD-'.Str::slug($product->sku.'-'.$size.'-'.$color)),
                            'stock' => fake()->numberBetween(0, 25),
                            'is_active' => true,
                        ]);
                    }
                }

                Review::factory()
                    ->count(fake()->numberBetween(1, 5))
                    ->for($product)
                    ->create();
            }
        }

        // Rattache quelques produits à des collections marketing
        Collection::query()->get()->each(function (Collection $collection) {
            $collection->products()->sync(
                Product::inRandomOrder()->limit(fake()->numberBetween(3, 6))->pluck('id')
            );
        });
    }

    private function seedNewsletter(): void
    {
        foreach (range(1, 15) as $i) {
            NewsletterSubscriber::updateOrCreate(
                ['email' => "abonne{$i}@example.com"],
                ['is_active' => true, 'subscribed_at' => now()->subDays(fake()->numberBetween(1, 200))]
            );
        }
    }
}
