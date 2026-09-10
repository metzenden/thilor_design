<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('shipping_method_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('coupon_id')->nullable()->constrained()->nullOnDelete();

            // Statut global de la commande (distinct du statut de paiement)
            $table->enum('status', ['pending', 'processing', 'shipped', 'delivered', 'cancelled'])
                ->default('pending');

            // Client (snapshot, valable aussi pour invités)
            $table->string('customer_name');
            $table->string('customer_email');
            $table->string('customer_phone', 30);

            // Adresse de livraison (snapshot historisé, jamais recalculé depuis addresses)
            $table->string('shipping_address_line');
            $table->string('shipping_city');
            $table->string('shipping_district')->nullable();
            $table->string('shipping_postal_code', 20)->nullable();
            $table->string('shipping_country')->default('Sénégal');

            $table->unsignedInteger('subtotal');
            $table->unsignedInteger('shipping_cost')->default(0);
            $table->unsignedInteger('discount_amount')->default(0);
            $table->unsignedInteger('total');

            $table->text('notes')->nullable();
            $table->timestamp('placed_at')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['status', 'placed_at']);
            $table->index('customer_email');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
