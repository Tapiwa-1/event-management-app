<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePackageTemplateRequest;
use App\Models\PackageTemplate;
use App\Models\Service;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class PackageTemplateController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Packages/Index', [
            'packages' => PackageTemplate::with('items.service')->orderBy('name')->paginate(12),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Packages/Create', [
            'services' => Service::where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function store(StorePackageTemplateRequest $request)
    {
        $package = DB::transaction(function () use ($request): PackageTemplate {
            $package = PackageTemplate::create($request->safe()->except('items') + [
                'is_active' => $request->boolean('is_active', true),
            ]);

            foreach ($request->validated('items') as $item) {
                $package->items()->create($item + [
                    'line_total' => round((float) $item['quantity'] * (float) $item['unit_price'], 2),
                ]);
            }

            $package->recalculateTotal();

            return $package;
        });

        return redirect()->route('packages.show', $package);
    }

    public function show(PackageTemplate $package): Response
    {
        return Inertia::render('Packages/Show', [
            'packageTemplate' => $package->load('items.service'),
        ]);
    }

    public function edit(PackageTemplate $package): Response
    {
        return Inertia::render('Packages/Edit', [
            'packageTemplate' => $package->load('items'),
            'services' => Service::where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function update(StorePackageTemplateRequest $request, PackageTemplate $package)
    {
        DB::transaction(function () use ($request, $package): void {
            $package->update($request->safe()->except('items') + [
                'is_active' => $request->boolean('is_active'),
            ]);
            $package->items()->delete();

            foreach ($request->validated('items') as $item) {
                $package->items()->create($item + [
                    'line_total' => round((float) $item['quantity'] * (float) $item['unit_price'], 2),
                ]);
            }

            $package->recalculateTotal();
        });

        return redirect()->route('packages.show', $package);
    }

    public function destroy(PackageTemplate $package)
    {
        $package->delete();

        return redirect()->route('packages.index');
    }
}
