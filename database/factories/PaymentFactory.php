<?php

namespace Database\Factories;

use App\Models\Payment;
use App\Models\Quotation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Payment>
 */
class PaymentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'quotation_id' => Quotation::factory(),
            'paid_at' => now()->toDateString(),
            'amount' => fake()->randomFloat(2, 100, 5000),
            'type' => 'Deposit',
            'method' => 'Bank Transfer',
            'reference' => fake()->bothify('PAY-####'),
            'notes' => null,
        ];
    }
}
