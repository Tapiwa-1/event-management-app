<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\Resource;
use App\Models\ResourceBooking;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ResourceBooking>
 */
class ResourceBookingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'resource_id' => Resource::factory(),
            'event_id' => Event::factory(),
            'booking_date' => now()->addWeek()->toDateString(),
            'quantity' => 1,
            'status' => 'Reserved',
            'notes' => null,
        ];
    }
}
