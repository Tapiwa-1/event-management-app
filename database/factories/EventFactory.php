<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Event>
 */
class EventFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'client_id' => Client::factory(),
            'name' => fake()->words(3, true),
            'type' => fake()->randomElement(['Wedding', 'Birthday', 'Corporate', 'Funeral', 'Graduation']),
            'event_date' => fake()->dateTimeBetween('+1 week', '+6 months'),
            'venue' => fake()->city().' Hall',
            'guest_count' => fake()->numberBetween(20, 300),
            'status' => 'Inquiry',
            'special_requirements' => fake()->optional()->sentence(),
        ];
    }
}
