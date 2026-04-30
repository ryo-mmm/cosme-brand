<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    public function definition(): array
    {
        $name = $this->faker->words(3, true);
        return [
            'name'               => $name,
            'slug'               => Str::slug($name) . '-' . $this->faker->unique()->randomNumber(4),
            'description'        => $this->faker->sentence(),
            'ingredients'        => $this->faker->sentence(),
            'price'              => $this->faker->numberBetween(3000, 10000),
            'subscription_price' => $this->faker->numberBetween(2500, 9000),
            'category'           => $this->faker->randomElement(['serum', 'lotion', 'cream', 'cleanser']),
            'skin_types'         => ['dry', 'normal'],
            'image'              => null,
            'volume_ml'          => $this->faker->randomElement([30, 50, 100, 200]),
            'is_active'          => true,
            'stock'              => $this->faker->numberBetween(10, 100),
        ];
    }

    public function inactive(): static
    {
        return $this->state(['is_active' => false]);
    }
}
