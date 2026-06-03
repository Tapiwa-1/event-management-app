<?php

namespace Database\Factories;

use App\Models\PackageTemplate;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PackageTemplate>
 */
class PackageTemplateFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->words(3, true),
            'event_type' => fake()->randomElement(['Wedding', 'Birthday', 'Corporate', 'Graduation']),
            'description' => fake()->sentence(),
            'total_amount' => 0,
            'is_active' => true,
        ];
    }
}
