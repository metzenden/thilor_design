<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\ProductVariant;
use App\Models\ShippingMethod;
use App\Models\User;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        // Clients de test avec adresse par défaut
        $clients = User::factory()
            ->count(6)
            ->has(\App\Models\Address::factory(), 'addresses')
            ->create();

        foreach ($clients as $client) {
            $client->syncRoles(['client']);
        }

        // Client nommé, pratique pour se connecter et tester "Mon compte"
        $fatou = User::factory()->create([
            'name' => 'Fatou Diop',
            'email' => 'fatou.diop@example.com',
            'phone' => '+221 77 123 45 67',
        ]);
        $fatou->syncRoles(['client']);
        \App\Models\Address::factory()->create([
            'user_id' => $fatou->id,
            'full_name' => 'Fatou Diop',
            'city' => 'Dakar',
            'district' => 'Liberté 6 Extension',
            'address_line' => 'Rue 10',
        ]);

        $allClients = $clients->push($fatou);
        $shippingMethods = ShippingMethod::all();
        $variants = ProductVariant::where('stock', '>', 0)->inRandomOrder()->limit(60)->get();

        if ($variants->isEmpty() || $shippingMethods->isEmpty()) {
            return; // pas assez de données produit/livraison pour générer des commandes
        }

        $statusPaymentMap = [
            'pending' => 'pending',
            'processing' => 'paid',
            'shipped' => 'paid',
            'delivered' => 'paid',
            'cancelled' => 'cancelled',
        ];

        foreach (range(1, 20) as $n) {
            $client = $allClients->random();
            $address = $client->addresses->first();
            $shippingMethod = $shippingMethods->random();
            $status = array_rand($statusPaymentMap);

            $items = $variants->random(min(fake()->numberBetween(1, 3), $variants->count()));
            $subtotal = 0;

            $order = Order::create([
                'order_number' => Order::generateOrderNumber(),
                'user_id' => $client->id,
                'shipping_method_id' => $shippingMethod->id,
                'status' => $status,
                'customer_name' => $address->full_name,
                'customer_email' => $client->email,
                'customer_phone' => $address->phone,
                'shipping_address_line' => $address->address_line,
                'shipping_city' => $address->city,
                'shipping_district' => $address->district,
                'shipping_country' => $address->country,
                'subtotal' => 0,
                'shipping_cost' => $shippingMethod->cost,
                'discount_amount' => 0,
                'total' => 0,
                'placed_at' => now()->subDays(fake()->numberBetween(0, 90)),
            ]);

            foreach ($items as $variant) {
                $quantity = fake()->numberBetween(1, 2);
                $unitPrice = $variant->price;
                $lineTotal = $unitPrice * $quantity;
                $subtotal += $lineTotal;

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $variant->product_id,
                    'product_variant_id' => $variant->id,
                    'product_name' => $variant->product->name,
                    'variant_label' => $variant->label,
                    'sku' => $variant->sku,
                    'unit_price' => $unitPrice,
                    'quantity' => $quantity,
                    'line_total' => $lineTotal,
                ]);
            }

            $total = $subtotal + $shippingMethod->cost;
            $order->update(['subtotal' => $subtotal, 'total' => $total]);

            Payment::create([
                'order_id' => $order->id,
                'method' => fake()->randomElement(['cash_on_delivery', 'card']),
                'status' => $statusPaymentMap[$status],
                'amount' => $total,
                'paid_at' => $statusPaymentMap[$status] === 'paid' ? $order->placed_at : null,
            ]);
        }
    }
}
