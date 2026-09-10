<?php

namespace App\Http\Controllers;

use App\Models\Product;

class ProductController extends Controller
{
    public function show(Product $product)
    {
        abort_unless($product->is_active, 404);

        $product->load(['images', 'variants' => fn ($q) => $q->where('is_active', true), 'category']);
        $product->increment('views_count');

        $reviews = $product->approvedReviews()->with('user')->latest()->paginate(5, ['*'], 'avis');

        $relatedProducts = Product::active()
            ->where('category_id', $product->category_id)
            ->whereKeyNot($product->id)
            ->with(['images', 'variants'])
            ->inRandomOrder()
            ->limit(4)
            ->get();

        $sizes = $product->variants->pluck('size')->unique()->filter()->values();
        $colors = $product->variants->pluck('color')->unique()->filter()->values();

        $canReview = auth()->check()
            && ! $product->reviews()->where('user_id', auth()->id())->exists();

        return view('products.show', compact(
            'product', 'reviews', 'relatedProducts', 'sizes', 'colors', 'canReview'
        ));
    }
}
