<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    public function index(Request $request)
    {
        return $this->render($request, Product::active(), 'Catalogue', null);
    }

    public function category(Request $request, Category $category)
    {
        $categoryIds = $category->children()->pluck('id')->push($category->id);

        return $this->render(
            $request,
            Product::active()->whereIn('category_id', $categoryIds),
            $category->name,
            $category
        );
    }

    public function promotions(Request $request)
    {
        return $this->render($request, Product::active()->onSale(), 'Promotions', null);
    }

    private function render(Request $request, $query, string $title, ?Category $activeCategory)
    {
        $query->with(['images', 'variants', 'category']);

        if ($search = $request->string('q')->trim()->value()) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('short_description', 'like', "%{$search}%")
                    ->orWhereHas('category', fn ($c) => $c->where('name', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('category') && ! $activeCategory) {
            $query->whereIn('category_id', (array) $request->input('category'));
        }

        if ($request->filled('size')) {
            $sizes = (array) $request->input('size');
            $query->whereHas('variants', fn ($v) => $v->whereIn('size', $sizes));
        }

        if ($request->filled('color')) {
            $colors = (array) $request->input('color');
            $query->whereHas('variants', fn ($v) => $v->whereIn('color', $colors));
        }

        if ($request->filled('min_price')) {
            $query->where('price', '>=', (int) $request->input('min_price'));
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', (int) $request->input('max_price'));
        }

        if ($request->boolean('in_stock')) {
            $query->whereHas('variants', fn ($v) => $v->where('stock', '>', 0));
        }

        match ($request->string('sort')->value()) {
            'price_asc' => $query->orderBy('price'),
            'price_desc' => $query->orderByDesc('price'),
            'popularity' => $query->orderByDesc('views_count'),
            default => $query->latest('published_at'),
        };

        $products = $query->paginate(12)->withQueryString();

        $categories = Category::where('is_active', true)->orderBy('position')->withCount('products')->get();

        return view('catalog.index', [
            'products' => $products,
            'categories' => $categories,
            'activeCategory' => $activeCategory,
            'title' => $title,
            'availableColors' => ['Bleu', 'Bordeaux', 'Doré', 'Vert', 'Noir', 'Orange'],
            'availableSizes' => ['S', 'M', 'L', 'XL', 'XXL'],
        ]);
    }
}
