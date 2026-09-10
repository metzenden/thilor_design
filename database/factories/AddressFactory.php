<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AddressFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'label' => 'Domicile',
            'full_name' => $this->faker->name(),
            'phone' => '+221 77 '.$this->faker->numerify('### ## ##'),
            'address_line' => $this->faker->streetAddress(),
            'city' => $this->faker->randomElement(['Dakar', 'Thiès', 'Saint-Louis', 'Mbour', 'Rufisque']),
            'district' => $this->faker->randomElement(['Plateau', 'Liberté', 'Sacré-Cœur', 'Ouakam', 'Mermoz']),
            'country' => 'Sénégal',
            'is_default' => true,
        ];
    }
}
