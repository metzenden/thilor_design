<?php

namespace Tests\Feature\Shop;

use App\Models\Category;
use App\Models\Collection;
use App\Models\Page;
use App\Models\Product;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicPagesSmokeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_all_public_pages_render_successfully(): void
    {
        $this->get('/')->assertOk();
        $this->get('/catalogue')->assertOk();
        $this->get('/catalogue?q=robe')->assertOk();
        $this->get('/promotions')->assertOk();
        $this->get('/collections')->assertOk();
        $this->get('/blog')->assertOk();
        $this->get('/contact')->assertOk();
        $this->get('/panier')->assertOk();
        $this->get('/sitemap.xml')->assertOk();

        $category = Category::firstOrFail();
        $this->get(route('catalog.category', $category))->assertOk();

        $collection = Collection::firstOrFail();
        $this->get(route('collections.show', $collection))->assertOk();

        $product = Product::with('images')->firstOrFail();
        $this->get(route('products.show', $product))->assertOk();

        $page = Page::where('type', 'page')->firstOrFail();
        $this->get(route('pages.show', $page))->assertOk();

        $article = Page::where('type', 'article')->firstOrFail();
        $this->get(route('blog.show', $article))->assertOk();
    }

    public function test_guest_cannot_access_account_pages(): void
    {
        $this->get('/compte')->assertRedirect('/login');
        $this->get('/favoris')->assertRedirect('/login');
    }
}
