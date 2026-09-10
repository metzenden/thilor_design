<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReviewRequest;
use App\Models\Product;

class ReviewController extends Controller
{
    public function store(StoreReviewRequest $request, Product $product)
    {
        abort_if(
            $product->reviews()->where('user_id', $request->user()->id)->exists(),
            403,
            'Vous avez déjà laissé un avis pour ce produit.'
        );

        $product->reviews()->create([
            ...$request->validated(),
            'user_id' => $request->user()->id,
            'is_approved' => false,
        ]);

        return back()->with('status', 'Merci ! Votre avis a été soumis et sera visible après validation.');
    }
}
