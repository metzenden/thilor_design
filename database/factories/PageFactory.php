<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PageFactory extends Factory
{
    public function definition(): array
    {
        $title = $this->faker->unique()->sentence(4);

        return [
            'type' => 'article',
            'title' => rtrim($title, '.'),
            'slug' => Str::slug($title).'-'.$this->faker->unique()->numberBetween(1, 99999),
            'excerpt' => $this->faker->sentence(20),
            'content' => '<p>'.implode('</p><p>', $this->faker->paragraphs(5)).'</p>',
            'is_published' => true,
            'published_at' => now()->subDays($this->faker->numberBetween(0, 60)),
        ];
    }
}
