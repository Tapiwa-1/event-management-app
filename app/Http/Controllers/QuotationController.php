<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreQuotationRequest;
use App\Models\Event;
use App\Models\Quotation;
use App\Models\Service;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class QuotationController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Quotations/Index', [
            'quotations' => Quotation::with(['event.client', 'payments'])
                ->latest('issued_at')
                ->paginate(12)
                ->through(fn (Quotation $quotation): array => [
                    'id' => $quotation->id,
                    'quotation_number' => $quotation->quotation_number,
                    'event' => $quotation->event,
                    'issued_at' => $quotation->issued_at,
                    'status' => $quotation->status,
                    'total_amount' => (float) $quotation->total_amount,
                    'paid_amount' => $quotation->paidAmount(),
                    'balance_due' => $quotation->balanceDue(),
                    'payment_status' => $quotation->paymentStatus(),
                ]),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Quotations/Create', [
            'events' => Event::with('client')->latest('event_date')->get(),
            'services' => Service::with('priceTiers')->where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function store(StoreQuotationRequest $request)
    {
        $quotation = DB::transaction(function () use ($request): Quotation {
            $quotation = Quotation::create($request->safe()->except('items') + [
                'quotation_number' => $this->nextQuotationNumber(),
            ]);

            foreach ($request->validated('items') as $item) {
                $quotation->items()->create($item + [
                    'line_total' => round((float) $item['quantity'] * (float) $item['unit_price'], 2),
                ]);
            }

            $quotation->recalculateTotal();
            $quotation->event()->update(['status' => $quotation->status === 'Accepted' ? 'Confirmed' : 'Quoted']);

            return $quotation;
        });

        return redirect()->route('quotations.show', $quotation);
    }

    public function show(Quotation $quotation): Response
    {
        return Inertia::render('Quotations/Show', [
            'quotation' => $quotation->load(['event.client', 'items.service', 'payments']),
            'paymentStatus' => $quotation->paymentStatus(),
            'balanceDue' => $quotation->balanceDue(),
        ]);
    }

    public function edit(Quotation $quotation): Response
    {
        return Inertia::render('Quotations/Edit', [
            'quotation' => $quotation->load('items'),
            'events' => Event::with('client')->latest('event_date')->get(),
            'services' => Service::with('priceTiers')->where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function update(StoreQuotationRequest $request, Quotation $quotation)
    {
        DB::transaction(function () use ($request, $quotation): void {
            $quotation->update($request->safe()->except('items'));
            $quotation->items()->delete();

            foreach ($request->validated('items') as $item) {
                $quotation->items()->create($item + [
                    'line_total' => round((float) $item['quantity'] * (float) $item['unit_price'], 2),
                ]);
            }

            $quotation->recalculateTotal();
        });

        return redirect()->route('quotations.show', $quotation);
    }

    public function destroy(Quotation $quotation)
    {
        $quotation->delete();

        return redirect()->route('quotations.index');
    }

    public function confirm(Quotation $quotation)
    {
        $quotation->update([
            'status' => 'Accepted',
            'confirmed_at' => now(),
        ]);

        $quotation->event()->update(['status' => 'Confirmed']);

        return redirect()->route('quotations.show', $quotation);
    }

    public function printable(Quotation $quotation): Response
    {
        return Inertia::render('Quotations/Print', [
            'quotation' => $quotation->load(['event.client', 'items.service']),
        ]);
    }

    private function nextQuotationNumber(): string
    {
        return 'QTN-'.now()->format('Ymd').'-'.str_pad((string) (Quotation::count() + 1), 4, '0', STR_PAD_LEFT);
    }
}
