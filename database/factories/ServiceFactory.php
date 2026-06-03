<?php

namespace Database\Factories;

use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Service>
 */
class ServiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->words(2, true),
            'category' => fake()->randomElement(['Decor', 'Audio', 'Media', 'Food', 'Logistics']),
            'description' => fake()->sentence(),
            'default_price' => fake()->randomFloat(2, 100, 5000),
            'unit' => fake()->randomElement(['each', 'event', 'day', 'guest']),
            'is_active' => true,
        ];
    }
}
