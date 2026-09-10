<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->restrictOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('sku')->unique();
            $table->text('short_description')->nullable();
            $table->longText('description')->nullable();
            $table->unsignedInteger('price'); // FCFA, unité entière
            $table->unsignedInteger('compare_at_price')->nullable(); // prix barré / promo
            $table->string('material')->nullable();
            $table->string('care_instructions')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->string('meta_title')->nullable();
            $table->string('meta_description', 500)->nullable();
            $table->unsignedInteger('views_count')->default(0);
            $table->softDeletes();
            $table->timestamps();

            $table->index(['is_active', 'category_id']);
            $table->index(['is_active', 'is_featured']);
            $table->index('published_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
