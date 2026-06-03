<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreResourceBookingRequest;
use App\Http\Requests\StoreResourceRequest;
use App\Models\Event;
use App\Models\Resource;
use App\Models\ResourceBooking;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class ResourceController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Resources/Index', [
            'resources' => Resource::with(['bookings.event.client'])->orderBy('type')->orderBy('name')->paginate(15),
            'events' => Event::with('client')->whereDate('event_date', '>=', today())->orderBy('event_date')->get(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Resources/Create');
    }

    public function store(StoreResourceRequest $request)
    {
        Resource::create($request->validated());

        return redirect()->route('resources.index');
    }

    public function show(Resource $resource): Response
    {
        return Inertia::render('Resources/Show', [
            'resource' => $resource->load(['bookings.event.client']),
            'events' => Event::with('client')->whereDate('event_date', '>=', today())->orderBy('event_date')->get(),
        ]);
    }

    public function edit(Resource $resource): Response
    {
        return Inertia::render('Resources/Edit', [
            'resource' => $resource,
        ]);
    }

    public function update(StoreResourceRequest $request, Resource $resource)
    {
        $resource->update($request->validated());

        return redirect()->route('resources.index');
    }

    public function destroy(Resource $resource)
    {
        $resource->delete();

        return redirect()->route('resources.index');
    }

    public function book(StoreResourceBookingRequest $request)
    {
        $resource = Resource::findOrFail($request->integer('resource_id'));
        $requestedQuantity = $request->integer('quantity');

        if ($resource->availableQuantityFor($request->string('booking_date')->toString()) < $requestedQuantity) {
            throw ValidationException::withMessages([
                'quantity' => 'This resource is not available in the requested quantity on that date.',
            ]);
        }

        ResourceBooking::create($request->validated());

        return redirect()->route('resources.index');
    }

    public function release(ResourceBooking $resourceBooking)
    {
        $resourceBooking->update(['status' => 'Released']);

        return redirect()->route('resources.index');
    }
}
