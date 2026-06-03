<?php

namespace Database\Factories;

use App\Models\Service;
use App\Models\ServicePriceTier;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ServicePriceTier>
 */
class ServicePriceTierFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'service_id' => Service::factory(),
            'name' => fake()->randomElement(['Small Package', 'Standard Package', 'Large Package']),
            'min_guests' => 1,
            'max_guests' => fake()->numberBetween(50, 300),
            'price' => fake()->randomFloat(2, 500, 5000),
        ];
    }
}
