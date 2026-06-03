<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\Expense;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Expense>
 */
class ExpenseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'event_id' => Event::factory(),
            'spent_at' => now()->toDateString(),
            'category' => fake()->randomElement(['Fuel', 'Food Ingredients', 'Staff Wages', 'Rentals', 'Transport', 'Other']),
            'description' => fake()->words(3, true),
            'amount' => fake()->randomFloat(2, 100, 5000),
            'supplier' => fake()->optional()->company(),
            'notes' => null,
        ];
    }
}
