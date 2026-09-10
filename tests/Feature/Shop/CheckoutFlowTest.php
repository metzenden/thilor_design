<?php

namespace Tests\Feature\Shop;

use App\Models\Coupon;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ShippingMethod;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckoutFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    private function makeVariant(int $stock = 5, int $price = 25000): ProductVariant
    {
        $product = Product::factory()->create(['price' => $price]);

        return ProductVariant::factory()->create([
            'product_id' => $product->id,
            'stock' => $stock,
        ]);
    }

    public function test_guest_can_add_to_cart_and_see_it_persist_across_requests(): void
    {
        $variant = $this->makeVariant(stock: 10);

        $this->post(route('cart.store'), ['variant_id' => $variant->id, 'quantity' => 2])
            ->assertRedirect();

        $response = $this->get(route('cart.index'));
        $response->assertOk();
        $response->assertSee($variant->product->name);
    }

    public function test_cannot_add_more_than_available_stock(): void
    {
        $variant = $this->makeVariant(stock: 2);

        $this->post(route('cart.store'), ['variant_id' => $variant->id, 'quantity' => 10]);

        $this->assertDatabaseHas('cart_items', [
            'product_variant_id' => $variant->id,
            'quantity' => 2, // plafonné au stock disponible
        ]);
    }

    public function test_full_checkout_creates_order_and_decrements_stock(): void
    {
        $variant = $this->makeVariant(stock: 5, price: 20000);
        $shippingMethod = ShippingMethod::create([
            'name' => 'Livraison à domicile', 'cost' => 2000, 'is_active' => true,
        ]);

        $this->post(route('cart.store'), ['variant_id' => $variant->id, 'quantity' => 3]);

        $this->post(route('checkout.information.store'), [
            'name' => 'Fatou Diop',
            'email' => 'fatou@example.com',
            'phone' => '+221771234567',
            'address_line' => 'Rue 10',
            'city' => 'Dakar',
            'shipping_method_id' => $shippingMethod->id,
        ])->assertRedirect(route('checkout.payment'));

        $response = $this->post(route('checkout.payment.store'), [
            'payment_method' => 'cash_on_delivery',
        ]);

        $order = Order::firstOrFail();
        $response->assertRedirect(route('checkout.confirmation', $order->order_number));

        // Prix historisé : 3 x 20000 = 60000, + 2000 livraison = 62000
        $this->assertSame(60000, $order->subtotal);
        $this->assertSame(2000, $order->shipping_cost);
        $this->assertSame(62000, $order->total);
        $this->assertSame('Fatou Diop', $order->customer_name);

        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->id,
            'quantity' => 3,
            'unit_price' => 20000,
        ]);

        $this->assertDatabaseHas('payments', [
            'order_id' => $order->id,
            'method' => 'cash_on_delivery',
            'status' => 'pending',
        ]);

        // Stock décrémenté
        $this->assertSame(2, $variant->fresh()->stock);

        // Panier vidé après commande
        $this->get(route('cart.index'))->assertOk()->assertSee('Votre panier est vide');

        // La confirmation est accessible
        $this->get(route('checkout.confirmation', $order->order_number))->assertOk();
    }

    public function test_checkout_rejects_unavailable_payment_gateway(): void
    {
        $variant = $this->makeVariant(stock: 5);
        $shippingMethod = ShippingMethod::create(['name' => 'Standard', 'cost' => 1000, 'is_active' => true]);

        $this->post(route('cart.store'), ['variant_id' => $variant->id, 'quantity' => 1]);
        $this->post(route('checkout.information.store'), [
            'name' => 'Test', 'email' => 'test@example.com', 'phone' => '+221700000000',
            'address_line' => 'Rue 1', 'city' => 'Dakar', 'shipping_method_id' => $shippingMethod->id,
        ]);

        $this->post(route('checkout.payment.store'), ['payment_method' => 'wave'])
            ->assertSessionHas('error');

        $this->assertDatabaseCount('orders', 0);
    }

    public function test_coupon_discount_is_applied_and_visible_in_cart(): void
    {
        $variant = $this->makeVariant(stock: 5, price: 10000);
        Coupon::create([
            'code' => 'PROMO10', 'type' => 'percent', 'value' => 10,
            'is_active' => true,
        ]);

        $this->post(route('cart.store'), ['variant_id' => $variant->id, 'quantity' => 1]);
        $this->post(route('cart.coupon.apply'), ['code' => 'promo10'])
            ->assertSessionHas('status');

        $this->get(route('cart.index'))->assertOk()->assertSee('PROMO10');
    }

    public function test_client_can_only_view_their_own_order(): void
    {
        $owner = User::factory()->create();
        $owner->assignRole('client');
        $other = User::factory()->create();
        $other->assignRole('client');

        $order = Order::create([
            'order_number' => Order::generateOrderNumber(),
            'user_id' => $owner->id,
            'status' => 'pending',
            'customer_name' => 'Owner', 'customer_email' => $owner->email, 'customer_phone' => '+221700000000',
            'shipping_address_line' => 'Rue 1', 'shipping_city' => 'Dakar',
            'subtotal' => 1000, 'shipping_cost' => 0, 'discount_amount' => 0, 'total' => 1000,
            'placed_at' => now(),
        ]);

        $this->actingAs($owner)->get(route('account.orders.show', $order))->assertOk();
        $this->actingAs($other)->get(route('account.orders.show', $order))->assertForbidden();
    }
}
