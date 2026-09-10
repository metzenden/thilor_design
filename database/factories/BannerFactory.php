<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class BannerFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(4),
            'subtitle' => $this->faker->sentence(8),
            'image' => 'banners/placeholder.jpg',
            'cta_label' => 'Découvrir la collection',
            'link' => '/catalogue',
            'position_key' => 'home_hero',
            'position' => 0,
            'is_active' => true,
        ];
    }
}
