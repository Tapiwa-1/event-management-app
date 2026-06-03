<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEventRequest;
use App\Models\Client;
use App\Models\Event;
use Inertia\Inertia;
use Inertia\Response;

class EventController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Events/Index', [
            'events' => Event::with('client')->latest('event_date')->paginate(12),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Events/Create', [
            'clients' => Client::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function store(StoreEventRequest $request)
    {
        $event = Event::create($request->validated());

        return redirect()->route('events.show', $event);
    }

    public function show(Event $event): Response
    {
        return Inertia::render('Events/Show', [
            'event' => $event->load(['client', 'quotations.items.service', 'quotations.payments', 'expenses', 'resourceBookings.resource']),
        ]);
    }

    public function edit(Event $event): Response
    {
        return Inertia::render('Events/Edit', [
            'event' => $event,
            'clients' => Client::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function update(StoreEventRequest $request, Event $event)
    {
        $event->update($request->validated());

        return redirect()->route('events.show', $event);
    }

    public function destroy(Event $event)
    {
        $event->delete();

        return redirect()->route('events.index');
    }
}
