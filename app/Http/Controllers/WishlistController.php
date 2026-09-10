<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function index(Request $request)
    {
        $products = $request->user()->wishlists()->with('product.images', 'product.variants')->paginate(12);

        return view('account.wishlist', compact('products'));
    }

    public function toggle(Request $request, Product $product)
    {
        $wishlist = $request->user()->wishlists()->where('product_id', $product->id)->first();

        if ($wishlist) {
            $wishlist->delete();
            $message = 'Retiré de vos favoris.';
        } else {
            $request->user()->wishlists()->create(['product_id' => $product->id]);
            $message = 'Ajouté à vos favoris.';
        }

        return back()->with('status', $message);
    }
}
