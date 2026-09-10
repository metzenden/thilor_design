<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductVariantFactory extends Factory
{
    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'size' => $this->faker->randomElement(['S', 'M', 'L', 'XL', 'XXL']),
            'color' => $this->faker->randomElement(['Bleu', 'Bordeaux', 'Doré', 'Vert', 'Noir', 'Orange']),
            'color_hex' => $this->faker->safeHexColor(),
            'sku' => strtoupper('TD-VAR-'.Str::random(10)),
            'stock' => $this->faker->numberBetween(0, 40),
            'is_active' => true,
        ];
    }
}
