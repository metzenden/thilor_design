<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddToCartRequest;
use App\Models\ProductVariant;
use App\Services\CartService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(private readonly CartService $cartService) {}

    public function index()
    {
        $cart = $this->cartService->current();

        return view('cart.index', compact('cart'));
    }

    public function store(AddToCartRequest $request)
    {
        $variant = ProductVariant::with('product')->findOrFail($request->validated('variant_id'));

        if ($variant->stock < 1) {
            return back()->with('error', 'Ce produit est en rupture de stock.');
        }

        $this->cartService->add($variant, $request->validated('quantity'));

        return back()->with('status', "« {$variant->product->name} » a été ajouté au panier.");
    }

    public function update(Request $request, int $item)
    {
        $request->validate(['quantity' => ['required', 'integer', 'min:0', 'max:20']]);

        $this->cartService->updateQuantity($item, (int) $request->input('quantity'));

        return back();
    }

    public function destroy(int $item)
    {
        $this->cartService->remove($item);

        return back()->with('status', 'Article retiré du panier.');
    }

    public function applyCoupon(Request $request)
    {
        $request->validate(['code' => ['required', 'string', 'max:50']]);

        [$success, $message] = $this->cartService->applyCoupon($request->input('code'));

        return back()->with($success ? 'status' : 'error', $message);
    }

    public function removeCoupon()
    {
        $this->cartService->removeCoupon();

        return back()->with('status', 'Code promo retiré.');
    }
}
