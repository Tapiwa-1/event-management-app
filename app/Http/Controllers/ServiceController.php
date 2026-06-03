<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreServiceRequest;
use App\Models\Service;
use Inertia\Inertia;
use Inertia\Response;

class ServiceController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Services/Index', [
            'services' => Service::orderBy('category')->orderBy('name')->paginate(20),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Services/Create');
    }

    public function store(StoreServiceRequest $request)
    {
        Service::create($request->validated() + ['is_active' => $request->boolean('is_active', true)]);

        return redirect()->route('services.index');
    }

    public function show(Service $service): Response
    {
        return Inertia::render('Services/Show', [
            'service' => $service->loadCount('quotationItems'),
        ]);
    }

    public function edit(Service $service): Response
    {
        return Inertia::render('Services/Edit', [
            'service' => $service,
        ]);
    }

    public function update(StoreServiceRequest $request, Service $service)
    {
        $service->update($request->validated() + ['is_active' => $request->boolean('is_active')]);

        return redirect()->route('services.index');
    }

    public function destroy(Service $service)
    {
        $service->delete();

        return redirect()->route('services.index');
    }
}
