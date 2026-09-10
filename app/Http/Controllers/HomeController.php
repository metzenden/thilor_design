<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\Category;
use App\Models\Collection;
use App\Models\Product;
use App\Models\Review;

class HomeController extends Controller
{
    public function __invoke()
    {
        $heroBanners = Banner::visible()->where('position_key', 'home_hero')->orderBy('position')->get();
        $secondaryBanners = Banner::visible()->where('position_key', 'home_secondary')->orderBy('position')->get();

        $categories = Category::where('is_active', true)->orderBy('position')->limit(5)->get();

        $newArrivals = Product::active()
            ->with(['images', 'variants'])
            ->latest('published_at')
            ->limit(8)
            ->get();

        $onSale = Product::active()->onSale()
            ->with(['images', 'variants'])
            ->limit(8)
            ->get();

        $featured = Product::active()->where('is_featured', true)
            ->with(['images', 'variants'])
            ->limit(8)
            ->get();

        $collections = Collection::where('is_active', true)->orderBy('position')->limit(3)->get();

        $reviews = Review::where('is_approved', true)
            ->with(['product', 'user'])
            ->latest()
            ->limit(6)
            ->get();

        return view('home', compact(
            'heroBanners', 'secondaryBanners', 'categories',
            'newArrivals', 'onSale', 'featured', 'collections', 'reviews'
        ));
    }
}
