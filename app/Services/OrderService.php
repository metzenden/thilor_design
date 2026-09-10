<?php

namespace App\Services;

use App\Contracts\PaymentGatewayContract;
use App\Models\Cart;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\ProductVariant;
use App\Models\ShippingMethod;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class OrderService
{
    /**
     * Transforme un panier en commande : verrouille les variantes (évite la survente
     * en cas de commandes concurrentes), historise prix/libellés, décrémente le stock,
     * puis amorce le paiement choisi. Toute l'opération est atomique.
     */
    public function createFromCart(
        Cart $cart,
        ShippingMethod $shippingMethod,
        array $customer,
        array $shippingAddress,
        PaymentGatewayContract $gateway,
        ?int $userId = null,
    ): Order {
        if ($cart->items->isEmpty()) {
            throw new RuntimeException('Le panier est vide.');
        }

        return DB::transaction(function () use ($cart, $shippingMethod, $customer, $shippingAddress, $gateway, $userId) {
            $variantIds = $cart->items->pluck('product_variant_id');

            // Verrouille les lignes de stock concernées pour éviter toute survente
            // en cas de commandes concurrentes sur les mêmes variantes.
            $variants = ProductVariant::whereIn('id', $variantIds)
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            foreach ($cart->items as $item) {
                $variant = $variants[$item->product_variant_id];
                if ($variant->stock < $item->quantity) {
                    throw new RuntimeException("Stock insuffisant pour « {$variant->product->name} » ({$variant->label}).");
                }
            }

            $subtotal = $cart->items->sum(fn ($item) => $variants[$item->product_variant_id]->price * $item->quantity);

            $coupon = $cart->coupon;
            $discount = ($coupon instanceof Coupon && $coupon->isValidFor($subtotal))
                ? $coupon->discountFor($subtotal)
                : 0;

            $total = max($subtotal - $discount, 0) + $shippingMethod->cost;

            $order = Order::create([
                'order_number' => Order::generateOrderNumber(),
                'user_id' => $userId,
                'shipping_method_id' => $shippingMethod->id,
                'coupon_id' => $discount > 0 ? $coupon->id : null,
                'status' => 'pending',
                'customer_name' => $customer['name'],
                'customer_email' => $customer['email'],
                'customer_phone' => $customer['phone'],
                'shipping_address_line' => $shippingAddress['address_line'],
                'shipping_city' => $shippingAddress['city'],
                'shipping_district' => $shippingAddress['district'] ?? null,
                'shipping_postal_code' => $shippingAddress['postal_code'] ?? null,
                'shipping_country' => $shippingAddress['country'] ?? 'Sénégal',
                'subtotal' => $subtotal,
                'shipping_cost' => $shippingMethod->cost,
                'discount_amount' => $discount,
                'total' => $total,
                'placed_at' => now(),
            ]);

            foreach ($cart->items as $item) {
                $variant = $variants[$item->product_variant_id];

                $order->items()->create([
                    'product_id' => $variant->product_id,
                    'product_variant_id' => $variant->id,
                    'product_name' => $variant->product->name,
                    'variant_label' => $variant->label,
                    'sku' => $variant->sku,
                    'unit_price' => $variant->price,
                    'quantity' => $item->quantity,
                    'line_total' => $variant->price * $item->quantity,
                ]);

                $variant->decrement('stock', $item->quantity);
            }

            if ($coupon && $discount > 0) {
                $coupon->increment('used_count');
            }

            $gateway->initiate($order);

            $cart->items()->delete();
            $cart->update(['coupon_id' => null]);

            return $order->fresh(['items', 'payment', 'shippingMethod']);
        });
    }
}
