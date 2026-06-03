<?php

namespace Database\Seeders;

use App\Models\Resource;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        collect([
            ['Decor', 'Decor', 2500, 'package'],
            ['PA System', 'Audio', 1800, 'package'],
            ['Photography', 'Media', 3500, 'event'],
            ['Videography', 'Media', 4500, 'event'],
            ['Catering/Food', 'Food', 120, 'guest'],
            ['Cakes', 'Food', 950, 'cake'],
            ['Tents', 'Infrastructure', 2200, 'tent'],
            ['Chairs and Tables', 'Furniture', 35, 'guest'],
            ['MC and Entertainment', 'Entertainment', 2500, 'event'],
            ['Transport', 'Logistics', 1200, 'trip'],
        ])->each(fn (array $service): Service => Service::firstOrCreate(
            ['name' => $service[0]],
            [
                'category' => $service[1],
                'default_price' => $service[2],
                'unit' => $service[3],
                'is_active' => true,
            ],
        ));

        $paSystem = Service::where('name', 'PA System')->first();

        if ($paSystem) {
            collect([
                ['Less than 100 people package', 1, 99, 1800],
                ['150 people package', 100, 150, 2500],
                ['250 people package', 151, 250, 3500],
            ])->each(fn (array $tier) => $paSystem->priceTiers()->updateOrCreate(
                ['name' => $tier[0]],
                [
                    'min_guests' => $tier[1],
                    'max_guests' => $tier[2],
                    'price' => $tier[3],
                ],
            ));
        }

        collect([
            ['Main PA System', 'PA System', 1],
            ['Camera Kit A', 'Camera', 1],
            ['Decor Inventory Set', 'Decor Inventory', 3],
            ['Event Staff', 'Staff', 8],
            ['Delivery Vehicle', 'Vehicle', 1],
        ])->each(fn (array $resource): Resource => Resource::firstOrCreate(
            ['name' => $resource[0]],
            [
                'type' => $resource[1],
                'quantity' => $resource[2],
                'status' => 'Available',
            ],
        ));

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}
