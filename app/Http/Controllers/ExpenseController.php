<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreExpenseRequest;
use App\Models\Event;
use App\Models\Expense;
use Inertia\Inertia;
use Inertia\Response;

class ExpenseController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Expenses/Index', [
            'expenses' => Expense::with('event.client')->latest('spent_at')->paginate(15),
            'events' => Event::with('client')->latest('event_date')->get(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Expenses/Create', [
            'events' => Event::with('client')->latest('event_date')->get(),
        ]);
    }

    public function store(StoreExpenseRequest $request)
    {
        Expense::create($request->validated());

        return redirect()->route('expenses.index');
    }

    public function show(Expense $expense): Response
    {
        return Inertia::render('Expenses/Show', [
            'expense' => $expense->load('event.client'),
        ]);
    }

    public function edit(Expense $expense): Response
    {
        return Inertia::render('Expenses/Edit', [
            'expense' => $expense,
            'events' => Event::with('client')->latest('event_date')->get(),
        ]);
    }

    public function update(StoreExpenseRequest $request, Expense $expense)
    {
        $expense->update($request->validated());

        return redirect()->route('expenses.index');
    }

    public function destroy(Expense $expense)
    {
        $expense->delete();

        return redirect()->route('expenses.index');
    }
}
