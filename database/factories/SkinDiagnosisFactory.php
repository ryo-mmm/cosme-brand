<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class SkinDiagnosisFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id'                 => User::factory(),
            'session_id'              => $this->faker->uuid(),
            'answers'                 => ['1' => 'a', '2' => 'b'],
            'skin_type'               => $this->faker->randomElement(['dry', 'oily', 'combination', 'sensitive', 'normal']),
            'score'                   => $this->faker->numberBetween(0, 100),
            'recommended_product_ids' => [],
        ];
    }

    public function guest(): static
    {
        return $this->state(['user_id' => null]);
    }
}
