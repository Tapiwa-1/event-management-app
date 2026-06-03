<?php

namespace Database\Factories;

use App\Models\PackageTemplate;
use App\Models\PackageTemplateItem;
use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PackageTemplateItem>
 */
class PackageTemplateItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $quantity = fake()->numberBetween(1, 5);
        $unitPrice = fake()->randomFloat(2, 100, 5000);

        return [
            'package_template_id' => PackageTemplate::factory(),
            'service_id' => Service::factory(),
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'line_total' => $quantity * $unitPrice,
        ];
    }
}
