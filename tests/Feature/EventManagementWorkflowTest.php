<?php

use App\Models\Client;
use App\Models\Event;
use App\Models\Expense;
use App\Models\Payment;
use App\Models\Quotation;
use App\Models\Resource;
use App\Models\ResourceBooking;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

uses(LazilyRefreshDatabase::class);

test('a quotation calculates package totals and can become a confirmed booking', function () {
    $user = User::factory()->create();
    $event = Event::factory()->create();
    $decor = Service::factory()->create(['name' => 'Decor', 'default_price' => 2500]);
    $paSystem = Service::factory()->create(['name' => 'PA System', 'default_price' => 1800]);

    $response = $this->actingAs($user)->post(route('quotations.store'), [
        'event_id' => $event->id,
        'issued_at' => now()->toDateString(),
        'valid_until' => now()->addDays(14)->toDateString(),
        'status' => 'Sent',
        'terms' => 'Deposit confirms booking.',
        'items' => [
            ['service_id' => $decor->id, 'description' => 'Decor', 'quantity' => 1, 'unit_price' => 2500],
            ['service_id' => $paSystem->id, 'description' => 'PA System', 'quantity' => 2, 'unit_price' => 1800],
        ],
    ]);

    $quotation = Quotation::firstOrFail();

    $response->assertRedirect(route('quotations.show', $quotation));
    expect((float) $quotation->fresh()->total_amount)->toBe(6100.0)
        ->and($event->fresh()->status)->toBe('Quoted');

    $this->actingAs($user)->post(route('quotations.confirm', $quotation))->assertRedirect(route('quotations.show', $quotation));

    expect($quotation->fresh()->status)->toBe('Accepted')
        ->and($event->fresh()->status)->toBe('Confirmed');
});

test('payments expose paid partial and outstanding balances', function () {
    $quotation = Quotation::factory()->create(['total_amount' => 10000]);

    expect($quotation->paymentStatus())->toBe('Outstanding')
        ->and($quotation->balanceDue())->toBe(10000.0);

    Payment::factory()->for($quotation)->create(['amount' => 4000]);

    expect($quotation->paymentStatus())->toBe('Partial')
        ->and($quotation->balanceDue())->toBe(6000.0);

    Payment::factory()->for($quotation)->create(['amount' => 6000, 'type' => 'Balance']);

    expect($quotation->paymentStatus())->toBe('Paid')
        ->and($quotation->balanceDue())->toBe(0.0);
});

test('event profit subtracts expenses from quoted revenue', function () {
    $event = Event::factory()->create();
    Quotation::factory()->for($event)->create(['status' => 'Accepted', 'total_amount' => 15000]);
    Expense::factory()->for($event)->create(['amount' => 3500]);
    Expense::factory()->for($event)->create(['amount' => 1500]);

    expect($event->profit())->toBe(10000.0);
});

test('resources cannot be double booked beyond available quantity', function () {
    $user = User::factory()->create();
    $client = Client::factory()->create();
    $event = Event::factory()->for($client)->create(['event_date' => '2026-08-15']);
    $secondEvent = Event::factory()->for($client)->create(['event_date' => '2026-08-15']);
    $resource = Resource::factory()->create(['quantity' => 1]);

    ResourceBooking::factory()->for($event)->for($resource)->create([
        'booking_date' => '2026-08-15',
        'quantity' => 1,
    ]);

    $this->actingAs($user)->post(route('resource-bookings.store'), [
        'resource_id' => $resource->id,
        'event_id' => $secondEvent->id,
        'booking_date' => '2026-08-15',
        'quantity' => 1,
        'status' => 'Reserved',
    ])->assertInvalid(['quantity']);
});
