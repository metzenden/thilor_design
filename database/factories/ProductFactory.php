<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    public function definition(): array
    {
        $name = $this->faker->unique()->words(3, true);
        $price = $this->faker->numberBetween(8, 60) * 1000;
        $onSale = $this->faker->boolean(30);

        return [
            'category_id' => Category::factory(),
            'name' => Str::title($name),
            'slug' => Str::slug($name).'-'.$this->faker->unique()->numberBetween(1, 999999),
            'sku' => strtoupper('TD-'.Str::random(8)),
            'short_description' => $this->faker->sentence(10),
            'description' => $this->faker->paragraphs(3, true),
            'price' => $price,
            'compare_at_price' => $onSale ? (int) round($price * 1.25 / 500) * 500 : null,
            'material' => $this->faker->randomElement(['Wax 100% coton', 'Bazin riche', 'Bogolan', 'Kente', 'Coton bio']),
            'care_instructions' => 'Lavage à la main recommandé.',
            'is_active' => true,
            'is_featured' => $this->faker->boolean(20),
            'published_at' => now()->subDays($this->faker->numberBetween(0, 120)),
        ];
    }
}
