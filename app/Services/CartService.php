<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Coupon;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class CartService
{
    private const GUEST_TOKEN_KEY = 'cart_guest_token';

    /**
     * Identifiant "invité" stable pour le panier, stocké comme attribut de session
     * plutôt que sur l'ID de session PHP brut : ce dernier peut être régénéré
     * (ex. connexion) sans que les données de session ne soient perdues, alors que
     * s'appuyer directement sur Session::getId() romprait le lien avec le panier.
     */
    private function guestToken(): string
    {
        $token = Session::get(self::GUEST_TOKEN_KEY);

        if (! $token) {
            $token = (string) Str::uuid();
            Session::put(self::GUEST_TOKEN_KEY, $token);
        }

        return $token;
    }

    /**
     * Nombre d'articles dans le panier, sans créer de panier vide en base
     * (utilisé par l'en-tête sur chaque page).
     */
    public function itemsCount(): int
    {
        $cart = Auth::check()
            ? Cart::where('user_id', Auth::id())->first()
            : Cart::where('session_id', Session::get(self::GUEST_TOKEN_KEY))->whereNull('user_id')->first();

        return $cart?->items()->sum('quantity') ?? 0;
    }

    public function current(): Cart
    {
        if (Auth::check()) {
            return Cart::firstOrCreate(['user_id' => Auth::id()])->load('items.variant.product', 'coupon');
        }

        return Cart::firstOrCreate(['session_id' => $this->guestToken(), 'user_id' => null])
            ->load('items.variant.product', 'coupon');
    }

    public function add(ProductVariant $variant, int $quantity = 1): Cart
    {
        $cart = $this->current();
        $quantity = max(1, $quantity);

        $item = $cart->items()->firstOrNew(['product_variant_id' => $variant->id]);
        $newQuantity = ($item->exists ? $item->quantity : 0) + $quantity;
        $item->quantity = min($newQuantity, max($variant->stock, 1));
        $item->cart_id = $cart->id;
        $item->save();

        return $this->current();
    }

    public function updateQuantity(int $itemId, int $quantity): Cart
    {
        $cart = $this->current();
        $item = $cart->items()->whereKey($itemId)->firstOrFail();

        if ($quantity < 1) {
            $item->delete();
        } else {
            $item->update(['quantity' => min($quantity, max($item->variant->stock, 1))]);
        }

        return $this->current();
    }

    public function remove(int $itemId): Cart
    {
        $cart = $this->current();
        $cart->items()->whereKey($itemId)->delete();

        return $this->current();
    }

    public function clear(): void
    {
        $cart = $this->current();
        $cart->items()->delete();
        $cart->update(['coupon_id' => null]);
    }

    public function applyCoupon(string $code): array
    {
        $cart = $this->current();
        $coupon = Coupon::whereRaw('UPPER(code) = ?', [strtoupper($code)])->first();

        if (! $coupon || ! $coupon->isValidFor($cart->subtotal)) {
            return [false, "Ce code promo n'est pas valide ou ne s'applique pas à votre panier."];
        }

        $cart->update(['coupon_id' => $coupon->id]);

        return [true, 'Code promo appliqué.'];
    }

    public function removeCoupon(): void
    {
        $this->current()->update(['coupon_id' => null]);
    }

    /**
     * Fusionne le panier "invité" (session) dans le panier du client qui vient de se connecter.
     * $guestToken doit être capturé AVANT session()->regenerate() (voir AppServiceProvider).
     */
    public function mergeGuestCartIntoUser(int $userId, ?string $guestToken): void
    {
        if (! $guestToken) {
            return;
        }

        $guestCart = Cart::where('session_id', $guestToken)->whereNull('user_id')->first();

        if (! $guestCart) {
            return;
        }

        $userCart = Cart::firstOrCreate(['user_id' => $userId]);

        foreach ($guestCart->items as $guestItem) {
            $existing = $userCart->items()->where('product_variant_id', $guestItem->product_variant_id)->first();

            if ($existing) {
                $existing->increment('quantity', $guestItem->quantity);
            } else {
                $guestItem->update(['cart_id' => $userCart->id]);
            }
        }

        $guestCart->delete();
    }
}
