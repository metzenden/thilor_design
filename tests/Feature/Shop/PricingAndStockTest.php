<?php

namespace Tests\Feature\Shop;

use App\Models\Coupon;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ShippingMethod;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PricingAndStockTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_discount_percent_is_computed_correctly(): void
    {
        $product = Product::factory()->create(['price' => 8000, 'compare_at_price' => 10000]);

        $this->assertSame(20, $product->discount_percent);
    }

    public function test_discount_percent_is_null_without_a_real_promotion(): void
    {
        $product = Product::factory()->create(['price' => 10000, 'compare_at_price' => null]);
        $this->assertNull($product->discount_percent);

        $productNoDiscount = Product::factory()->create(['price' => 10000, 'compare_at_price' => 9000]);
        $this->assertNull($productNoDiscount->discount_percent);
    }

    public function test_percent_coupon_never_discounts_more_than_the_subtotal(): void
    {
        $coupon = Coupon::create(['code' => 'MEGA90', 'type' => 'percent', 'value' => 90, 'is_active' => true]);

        // 90% de 1000 = 900, ne doit jamais dépasser le sous-total.
        $this->assertSame(900, $coupon->discountFor(1000));
    }

    public function test_fixed_coupon_is_capped_to_the_subtotal(): void
    {
        $coupon = Coupon::create(['code' => 'FIXED50000', 'type' => 'fixed', 'value' => 50000, 'is_active' => true]);

        // Le coupon fixe ne doit jamais rendre le total négatif.
        $this->assertSame(5000, $coupon->discountFor(5000));
    }

    public function test_coupon_below_minimum_amount_is_invalid(): void
    {
        $coupon = Coupon::create([
            'code' => 'MIN20000', 'type' => 'fixed', 'value' => 1000,
            'min_amount' => 20000, 'is_active' => true,
        ]);

        $this->assertFalse($coupon->isValidFor(15000));
        $this->assertTrue($coupon->isValidFor(20000));
    }

    public function test_expired_coupon_is_rejected_at_checkout(): void
    {
        Coupon::create([
            'code' => 'EXPIRED', 'type' => 'percent', 'value' => 10,
            'ends_at' => now()->subDay(), 'is_active' => true,
        ]);

        $product = Product::factory()->create(['price' => 10000]);
        $variant = ProductVariant::factory()->create(['product_id' => $product->id, 'stock' => 5]);
        $this->post(route('cart.store'), ['variant_id' => $variant->id, 'quantity' => 1]);

        $this->post(route('cart.coupon.apply'), ['code' => 'EXPIRED'])
            ->assertSessionHas('error');
    }

    public function test_checkout_fails_gracefully_when_stock_runs_out_between_add_and_checkout(): void
    {
        $product = Product::factory()->create(['price' => 15000]);
        $variant = ProductVariant::factory()->create(['product_id' => $product->id, 'stock' => 3]);
        $shippingMethod = ShippingMethod::create(['name' => 'Standard', 'cost' => 1000, 'is_active' => true]);

        $this->post(route('cart.store'), ['variant_id' => $variant->id, 'quantity' => 3]);

        // Le stock est vendu ailleurs entre-temps (ex. une autre commande concurrente).
        $variant->update(['stock' => 0]);

        $this->post(route('checkout.information.store'), [
            'name' => 'Test', 'email' => 'test@example.com', 'phone' => '+221700000000',
            'address_line' => 'Rue 1', 'city' => 'Dakar', 'shipping_method_id' => $shippingMethod->id,
        ]);

        $this->post(route('checkout.payment.store'), ['payment_method' => 'cash_on_delivery'])
            ->assertSessionHas('error');

        $this->assertDatabaseCount('orders', 0);
    }
}
