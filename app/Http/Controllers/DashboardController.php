<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Expense;
use App\Models\Payment;
use App\Models\Quotation;
use App\Models\Service;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(): Response
    {
        $monthStart = now()->startOfMonth();
        $monthEnd = now()->endOfMonth();
        $monthlyRevenue = Payment::whereBetween('paid_at', [$monthStart, $monthEnd])->sum('amount');
        $monthlyExpenses = Expense::whereBetween('spent_at', [$monthStart, $monthEnd])->sum('amount');

        return Inertia::render('Dashboard', [
            'stats' => [
                'upcoming_events' => Event::whereDate('event_date', '>=', today())->count(),
                'monthly_revenue' => (float) $monthlyRevenue,
                'monthly_expenses' => (float) $monthlyExpenses,
                'monthly_profit' => (float) $monthlyRevenue - (float) $monthlyExpenses,
                'outstanding_payments' => Quotation::whereIn('status', ['Sent', 'Accepted'])
                    ->get()
                    ->sum(fn (Quotation $quotation): float => $quotation->balanceDue()),
            ],
            'upcomingEvents' => Event::with('client')
                ->whereDate('event_date', '>=', today())
                ->orderBy('event_date')
                ->limit(6)
                ->get(),
            'calendarEvents' => Event::with('client')
                ->whereBetween('event_date', [today(), today()->addDays(45)])
                ->orderBy('event_date')
                ->get()
                ->map(fn (Event $event): array => [
                    'id' => $event->id,
                    'name' => $event->name,
                    'client' => $event->client->name,
                    'date' => $event->event_date->toDateString(),
                    'status' => $event->status,
                ]),
            'popularServices' => Service::query()
                ->leftJoin('quotation_items', 'services.id', '=', 'quotation_items.service_id')
                ->select('services.id', 'services.name', DB::raw('COUNT(quotation_items.id) as usage_count'))
                ->groupBy('services.id', 'services.name')
                ->orderByDesc('usage_count')
                ->limit(5)
                ->get(),
            'profitByEvent' => Event::query()
                ->withSum('expenses', 'amount')
                ->withSum(['quotations as quoted_total' => fn ($query) => $query->whereIn('status', ['Sent', 'Accepted'])], 'total_amount')
                ->latest('event_date')
                ->limit(6)
                ->get()
                ->map(fn (Event $event): array => [
                    'id' => $event->id,
                    'name' => $event->name,
                    'profit' => (float) ($event->quoted_total ?? 0) - (float) ($event->expenses_sum_amount ?? 0),
                ]),
            'monthLabel' => Carbon::now()->format('F Y'),
        ]);
    }
}
