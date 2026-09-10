<?php

namespace App\Http\Controllers;

use App\Models\Collection;

class CollectionController extends Controller
{
    public function index()
    {
        $collections = Collection::where('is_active', true)->orderBy('position')->withCount('products')->get();

        return view('collections.index', compact('collections'));
    }

    public function show(Collection $collection)
    {
        $products = $collection->products()
            ->active()
            ->with(['images', 'variants'])
            ->paginate(12);

        return view('collections.show', compact('collection', 'products'));
    }
}
