<?php

namespace Database\Factories;

use App\Models\Resource;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<resource>
 */
class ResourceFactory extends Factory
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
            'type' => fake()->randomElement(['PA System', 'Camera', 'Decor Inventory', 'Staff', 'Vehicle', 'Other']),
            'quantity' => fake()->numberBetween(1, 8),
            'status' => 'Available',
            'notes' => null,
        ];
    }
}
