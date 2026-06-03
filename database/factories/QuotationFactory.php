<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\Quotation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Quotation>
 */
class QuotationFactory extends Factory
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
            'quotation_number' => 'QTN-'.fake()->unique()->numerify('######'),
            'issued_at' => now()->toDateString(),
            'valid_until' => now()->addDays(14)->toDateString(),
            'status' => 'Sent',
            'total_amount' => 0,
            'terms' => 'Deposit required to confirm booking.',
        ];
    }
}
