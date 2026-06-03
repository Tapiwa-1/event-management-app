<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClientRequest;
use App\Models\Client;
use Inertia\Inertia;
use Inertia\Response;

class ClientController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Clients/Index', [
            'clients' => Client::withCount('events')->latest()->paginate(12),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Clients/Create');
    }

    public function store(StoreClientRequest $request)
    {
        $client = Client::create($request->validated());

        return redirect()->route('clients.show', $client);
    }

    public function show(Client $client): Response
    {
        return Inertia::render('Clients/Show', [
            'client' => $client->load(['events.quotations.payments', 'events.expenses']),
        ]);
    }

    public function edit(Client $client): Response
    {
        return Inertia::render('Clients/Edit', [
            'client' => $client,
        ]);
    }

    public function update(StoreClientRequest $request, Client $client)
    {
        $client->update($request->validated());

        return redirect()->route('clients.show', $client);
    }

    public function destroy(Client $client)
    {
        $client->delete();

        return redirect()->route('clients.index');
    }
}
