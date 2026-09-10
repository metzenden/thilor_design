<?php

namespace Tests\Feature\Shop;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatalogSearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_search_finds_matching_product_by_name(): void
    {
        Product::factory()->create(['name' => 'Robe en Wax Ankara Unique']);
        Product::factory()->create(['name' => 'Chemise Bazin Introuvable']);

        $response = $this->get('/catalogue?q=Ankara');

        $response->assertOk();
        $response->assertSee('Robe en Wax Ankara Unique');
        $response->assertDontSee('Chemise Bazin Introuvable');
    }

    public function test_category_filter_only_shows_products_from_that_category(): void
    {
        $femme = Category::factory()->create(['name' => 'Femme']);
        $homme = Category::factory()->create(['name' => 'Homme']);

        $robe = Product::factory()->create(['category_id' => $femme->id, 'name' => 'Robe Femme Test']);
        Product::factory()->create(['category_id' => $homme->id, 'name' => 'Chemise Homme Test']);

        $response = $this->get(route('catalog.category', $femme));

        $response->assertOk();
        $response->assertSee('Robe Femme Test');
        $response->assertDontSee('Chemise Homme Test');
    }

    public function test_price_sort_orders_products_correctly(): void
    {
        Product::factory()->create(['name' => 'Produit Cher', 'price' => 90000]);
        Product::factory()->create(['name' => 'Produit Pas Cher', 'price' => 10000]);

        $response = $this->get('/catalogue?sort=price_asc');
        $response->assertOk();

        $content = $response->getContent();
        $posCheap = strpos($content, 'Produit Pas Cher');
        $posExpensive = strpos($content, 'Produit Cher');

        $this->assertNotFalse($posCheap);
        $this->assertNotFalse($posExpensive);
        $this->assertLessThan($posExpensive, $posCheap);
    }

    public function test_inactive_product_is_not_listed_or_directly_accessible(): void
    {
        $product = Product::factory()->create(['name' => 'Produit Inactif', 'is_active' => false]);

        $this->get('/catalogue')->assertOk()->assertDontSee('Produit Inactif');
        $this->get(route('products.show', $product))->assertNotFound();
    }

    public function test_unknown_product_returns_404(): void
    {
        $this->get('/produit/ce-produit-n-existe-pas')->assertNotFound();
    }
}
