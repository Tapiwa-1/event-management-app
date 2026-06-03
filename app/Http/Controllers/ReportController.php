<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Event;
use App\Models\Expense;
use App\Models\Payment;
use App\Models\Service;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class ReportController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Reports/Index', [
            'revenueByMonth' => Payment::query()
                ->selectRaw("strftime('%Y-%m', paid_at) as month, SUM(amount) as total")
                ->groupBy('month')
                ->orderBy('month')
                ->get(),
            'expensesByMonth' => Expense::query()
                ->selectRaw("strftime('%Y-%m', spent_at) as month, SUM(amount) as total")
                ->groupBy('month')
                ->orderBy('month')
                ->get(),
            'profitByEvent' => Event::query()
                ->with('client')
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
            'clientSummary' => Client::withCount('events')->orderByDesc('events_count')->limit(20)->get(),
            'servicePerformance' => Service::query()
                ->leftJoin('quotation_items', 'services.id', '=', 'quotation_items.service_id')
                ->select('services.name', DB::raw('COUNT(quotation_items.id) as usage_count'), DB::raw('COALESCE(SUM(quotation_items.line_total), 0) as revenue'))
                ->groupBy('services.id', 'services.name')
                ->orderByDesc('revenue')
                ->get(),
        ]);
    }
}
