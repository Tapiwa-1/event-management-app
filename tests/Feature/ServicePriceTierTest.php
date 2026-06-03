<?php

use App\Models\Service;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

uses(LazilyRefreshDatabase::class);

test('services can resolve prices by guest count tier', function () {
    $service = Service::factory()->create(['default_price' => 1000]);

    $service->priceTiers()->createMany([
        ['name' => 'Less than 100 people package', 'min_guests' => 1, 'max_guests' => 99, 'price' => 1800],
        ['name' => '150 people package', 'min_guests' => 100, 'max_guests' => 150, 'price' => 2500],
        ['name' => '250 people package', 'min_guests' => 151, 'max_guests' => 250, 'price' => 3500],
    ]);

    expect($service->priceForGuestCount(80))->toBe(1800.0)
        ->and($service->priceForGuestCount(150))->toBe(2500.0)
        ->and($service->priceForGuestCount(200))->toBe(3500.0)
        ->and($service->priceForGuestCount(300))->toBe(1000.0);
});

test('the pa system seed includes guest count packages', function () {
    $this->seed(DatabaseSeeder::class);

    $paSystem = Service::where('name', 'PA System')->with('priceTiers')->firstOrFail();

    expect($paSystem->priceTiers)->toHaveCount(3)
        ->and($paSystem->priceForGuestCount(75))->toBe(1800.0)
        ->and($paSystem->priceForGuestCount(125))->toBe(2500.0)
        ->and($paSystem->priceForGuestCount(225))->toBe(3500.0);
});
