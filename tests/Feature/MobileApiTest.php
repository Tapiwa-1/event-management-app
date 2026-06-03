<?php

use App\Models\Client;
use App\Models\Event;
use App\Models\Quotation;
use App\Models\Resource;
use App\Models\ResourceBooking;
use App\Models\Service;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

uses(LazilyRefreshDatabase::class);

test('mobile bootstrap returns the app data needed by flutter', function () {
    $client = Client::factory()->create();
    $event = Event::factory()->for($client)->create();
    $service = Service::factory()->create(['name' => 'PA System']);
    $quotation = Quotation::factory()->for($event)->create(['total_amount' => 2500]);

    $response = $this->getJson('/api/v1/mobile/bootstrap');

    $response->assertOk()
        ->assertJsonPath('clients.0.id', $client->id)
        ->assertJsonPath('events.0.id', $event->id)
        ->assertJsonPath('services.0.id', $service->id)
        ->assertJsonPath('quotations.0.id', $quotation->id)
        ->assertJsonStructure([
            'dashboard' => [
                'stats' => [
                    'upcoming_events',
                    'monthly_revenue',
                    'monthly_expenses',
                    'monthly_profit',
                    'outstanding_payments',
                ],
            ],
            'reports',
        ]);
});

test('mobile api creates a client and event', function () {
    $clientResponse = $this->postJson('/api/v1/clients', [
        'name' => 'Avery Stone',
        'email' => 'avery@example.com',
        'phone' => '555-0199',
    ]);

    $clientResponse->assertCreated()->assertJsonPath('name', 'Avery Stone');

    $eventResponse = $this->postJson('/api/v1/events', [
        'client_id' => $clientResponse->json('id'),
        'name' => 'Avery Wedding',
        'type' => 'Wedding',
        'event_date' => now()->addMonth()->toDateString(),
        'venue' => 'Garden Hall',
        'guest_count' => 150,
        'status' => 'Inquiry',
    ]);

    $eventResponse->assertCreated()
        ->assertJsonPath('name', 'Avery Wedding')
        ->assertJsonPath('client.name', 'Avery Stone');
});

test('mobile api creates a quotation and prevents resource overbooking', function () {
    $event = Event::factory()->create(['event_date' => '2026-09-20']);
    $secondEvent = Event::factory()->create(['event_date' => '2026-09-20']);
    $service = Service::factory()->create(['default_price' => 1800]);
    $resource = Resource::factory()->create(['quantity' => 1]);

    $quoteResponse = $this->postJson('/api/v1/quotations', [
        'event_id' => $event->id,
        'issued_at' => now()->toDateString(),
        'status' => 'Sent',
        'terms' => 'Deposit required.',
        'items' => [
            [
                'service_id' => $service->id,
                'description' => 'PA System',
                'quantity' => 1,
                'unit_price' => 1800,
            ],
        ],
    ]);

    $quoteResponse->assertCreated()
        ->assertJsonPath('total_amount', '1800.00');

    ResourceBooking::factory()->for($event)->for($resource)->create([
        'booking_date' => '2026-09-20',
        'quantity' => 1,
    ]);

    $this->postJson('/api/v1/resource-bookings', [
        'resource_id' => $resource->id,
        'event_id' => $secondEvent->id,
        'booking_date' => '2026-09-20',
        'quantity' => 1,
        'status' => 'Reserved',
    ])->assertUnprocessable()
        ->assertJsonValidationErrors('quantity');
});
