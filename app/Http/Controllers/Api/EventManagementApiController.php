<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreClientRequest;
use App\Http\Requests\StoreEventRequest;
use App\Http\Requests\StoreExpenseRequest;
use App\Http\Requests\StorePackageTemplateRequest;
use App\Http\Requests\StorePaymentRequest;
use App\Http\Requests\StoreQuotationRequest;
use App\Http\Requests\StoreResourceBookingRequest;
use App\Http\Requests\StoreResourceRequest;
use App\Http\Requests\StoreServiceRequest;
use App\Models\Client;
use App\Models\Event;
use App\Models\Expense;
use App\Models\PackageTemplate;
use App\Models\Payment;
use App\Models\Quotation;
use App\Models\Resource;
use App\Models\ResourceBooking;
use App\Models\Service;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class EventManagementApiController extends Controller
{
    public function bootstrap(): JsonResponse
    {
        return response()->json([
            'dashboard' => $this->dashboardPayload(),
            'clients' => Client::withCount('events')->latest()->get(),
            'events' => Event::with('client')->latest('event_date')->get(),
            'services' => Service::with('priceTiers')->orderBy('category')->orderBy('name')->get(),
            'packages' => PackageTemplate::with('items.service')->orderBy('name')->get(),
            'quotations' => $this->quotationQuery()->get()->map(fn (Quotation $quotation): array => $this->quotationSummary($quotation)),
            'payments' => Payment::with('quotation.event.client')->latest('paid_at')->get(),
            'resources' => Resource::with('bookings.event.client')->orderBy('type')->orderBy('name')->get(),
            'expenses' => Expense::with('event.client')->latest('spent_at')->get(),
            'reports' => $this->reportsPayload(),
        ]);
    }

    public function dashboard(): JsonResponse
    {
        return response()->json($this->dashboardPayload());
    }

    public function reports(): JsonResponse
    {
        return response()->json($this->reportsPayload());
    }

    public function clients(): JsonResponse
    {
        return response()->json(Client::withCount('events')->latest()->get());
    }

    public function storeClient(StoreClientRequest $request): JsonResponse
    {
        return response()->json(Client::create($request->validated()), 201);
    }

    public function updateClient(StoreClientRequest $request, Client $client): JsonResponse
    {
        $client->update($request->validated());

        return response()->json($client);
    }

    public function events(): JsonResponse
    {
        return response()->json(Event::with('client')->latest('event_date')->get());
    }

    public function storeEvent(StoreEventRequest $request): JsonResponse
    {
        return response()->json(Event::with('client')->find(Event::create($request->validated())->id), 201);
    }

    public function updateEvent(StoreEventRequest $request, Event $event): JsonResponse
    {
        $event->update($request->validated());

        return response()->json($event->load('client'));
    }

    public function services(): JsonResponse
    {
        return response()->json(Service::with('priceTiers')->orderBy('category')->orderBy('name')->get());
    }

    public function storeService(StoreServiceRequest $request): JsonResponse
    {
        return response()->json(Service::create($request->validated() + [
            'is_active' => $request->boolean('is_active', true),
        ]), 201);
    }

    public function quotations(): JsonResponse
    {
        return response()->json($this->quotationQuery()->get()->map(fn (Quotation $quotation): array => $this->quotationSummary($quotation)));
    }

    public function quotation(Quotation $quotation): JsonResponse
    {
        return response()->json($quotation->load(['event.client', 'items.service', 'payments']));
    }

    public function storeQuotation(StoreQuotationRequest $request): JsonResponse
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

        return response()->json($quotation->load(['event.client', 'items.service', 'payments']), 201);
    }

    public function confirmQuotation(Quotation $quotation): JsonResponse
    {
        $quotation->update([
            'status' => 'Accepted',
            'confirmed_at' => now(),
        ]);

        $quotation->event()->update(['status' => 'Confirmed']);

        return response()->json($quotation->load(['event.client', 'items.service', 'payments']));
    }

    public function payments(): JsonResponse
    {
        return response()->json(Payment::with('quotation.event.client')->latest('paid_at')->get());
    }

    public function storePayment(StorePaymentRequest $request): JsonResponse
    {
        return response()->json(Payment::with('quotation.event.client')->find(Payment::create($request->validated())->id), 201);
    }

    public function resources(): JsonResponse
    {
        return response()->json(Resource::with('bookings.event.client')->orderBy('type')->orderBy('name')->get());
    }

    public function storeResource(StoreResourceRequest $request): JsonResponse
    {
        return response()->json(Resource::create($request->validated()), 201);
    }

    public function bookResource(StoreResourceBookingRequest $request): JsonResponse
    {
        $resource = Resource::findOrFail($request->integer('resource_id'));
        $requestedQuantity = $request->integer('quantity');

        if ($resource->availableQuantityFor($request->string('booking_date')->toString()) < $requestedQuantity) {
            throw ValidationException::withMessages([
                'quantity' => 'This resource is not available in the requested quantity on that date.',
            ]);
        }

        return response()->json(ResourceBooking::with(['resource', 'event.client'])->find(ResourceBooking::create($request->validated())->id), 201);
    }

    public function expenses(): JsonResponse
    {
        return response()->json(Expense::with('event.client')->latest('spent_at')->get());
    }

    public function storeExpense(StoreExpenseRequest $request): JsonResponse
    {
        return response()->json(Expense::with('event.client')->find(Expense::create($request->validated())->id), 201);
    }

    public function packages(): JsonResponse
    {
        return response()->json(PackageTemplate::with('items.service')->orderBy('name')->get());
    }

    public function storePackage(StorePackageTemplateRequest $request): JsonResponse
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

        return response()->json($package->load('items.service'), 201);
    }

    private function dashboardPayload(): array
    {
        $monthStart = now()->startOfMonth();
        $monthEnd = now()->endOfMonth();
        $monthlyRevenue = Payment::whereBetween('paid_at', [$monthStart, $monthEnd])->sum('amount');
        $monthlyExpenses = Expense::whereBetween('spent_at', [$monthStart, $monthEnd])->sum('amount');

        return [
            'stats' => [
                'upcoming_events' => Event::whereDate('event_date', '>=', today())->count(),
                'monthly_revenue' => (float) $monthlyRevenue,
                'monthly_expenses' => (float) $monthlyExpenses,
                'monthly_profit' => (float) $monthlyRevenue - (float) $monthlyExpenses,
                'outstanding_payments' => Quotation::whereIn('status', ['Sent', 'Accepted'])->get()->sum(fn (Quotation $quotation): float => $quotation->balanceDue()),
            ],
            'upcoming_events' => Event::with('client')->whereDate('event_date', '>=', today())->orderBy('event_date')->limit(8)->get(),
            'popular_services' => Service::query()
                ->leftJoin('quotation_items', 'services.id', '=', 'quotation_items.service_id')
                ->select('services.id', 'services.name', DB::raw('COUNT(quotation_items.id) as usage_count'))
                ->groupBy('services.id', 'services.name')
                ->orderByDesc('usage_count')
                ->limit(5)
                ->get(),
        ];
    }

    private function reportsPayload(): array
    {
        return [
            'profit_by_event' => Event::with('client')
                ->withSum('expenses', 'amount')
                ->withSum(['quotations as revenue_total' => fn ($query) => $query->whereIn('status', ['Sent', 'Accepted'])], 'total_amount')
                ->latest('event_date')
                ->get()
                ->map(fn (Event $event): array => [
                    'id' => $event->id,
                    'name' => $event->name,
                    'client' => $event->client->name,
                    'revenue' => (float) ($event->revenue_total ?? 0),
                    'expenses' => (float) ($event->expenses_sum_amount ?? 0),
                    'profit' => (float) ($event->revenue_total ?? 0) - (float) ($event->expenses_sum_amount ?? 0),
                ]),
            'service_performance' => Service::query()
                ->leftJoin('quotation_items', 'services.id', '=', 'quotation_items.service_id')
                ->select('services.name', DB::raw('COUNT(quotation_items.id) as usage_count'), DB::raw('COALESCE(SUM(quotation_items.line_total), 0) as revenue'))
                ->groupBy('services.id', 'services.name')
                ->orderByDesc('revenue')
                ->get(),
        ];
    }

    private function quotationQuery()
    {
        return Quotation::with(['event.client', 'items.service', 'payments'])->latest('issued_at');
    }

    private function quotationSummary(Quotation $quotation): array
    {
        return [
            'id' => $quotation->id,
            'quotation_number' => $quotation->quotation_number,
            'event' => $quotation->event,
            'issued_at' => $quotation->issued_at,
            'status' => $quotation->status,
            'total_amount' => (float) $quotation->total_amount,
            'paid_amount' => $quotation->paidAmount(),
            'balance_due' => $quotation->balanceDue(),
            'payment_status' => $quotation->paymentStatus(),
        ];
    }

    private function nextQuotationNumber(): string
    {
        return 'QTN-'.now()->format('Ymd').'-'.str_pad((string) (Quotation::count() + 1), 4, '0', STR_PAD_LEFT);
    }
}
