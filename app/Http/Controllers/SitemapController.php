<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Collection;
use App\Models\Page;
use App\Models\Product;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Response;

class SitemapController extends Controller
{
    public function __invoke()
    {
        $xml = Cache::remember('sitemap.xml', now()->addHours(6), function () {
            $urls = collect([
                ['loc' => route('home'), 'priority' => '1.0'],
                ['loc' => route('catalog.index'), 'priority' => '0.9'],
                ['loc' => route('collections.index'), 'priority' => '0.7'],
                ['loc' => route('blog.index'), 'priority' => '0.6'],
                ['loc' => route('contact.create'), 'priority' => '0.5'],
            ]);

            Category::where('is_active', true)->get()->each(function ($category) use ($urls) {
                $urls->push(['loc' => route('catalog.category', $category), 'priority' => '0.8']);
            });

            Collection::where('is_active', true)->get()->each(function ($collection) use ($urls) {
                $urls->push(['loc' => route('collections.show', $collection), 'priority' => '0.7']);
            });

            Product::active()->get()->each(function ($product) use ($urls) {
                $urls->push([
                    'loc' => route('products.show', $product),
                    'priority' => '0.8',
                    'lastmod' => $product->updated_at->toAtomString(),
                ]);
            });

            Page::published()->get()->each(function ($page) use ($urls) {
                $route = $page->type === 'article' ? route('blog.show', $page) : route('pages.show', $page);
                $urls->push(['loc' => $route, 'priority' => '0.5']);
            });

            return view('sitemap', compact('urls'))->render();
        });

        return Response::make($xml, 200, ['Content-Type' => 'application/xml']);
    }
}
