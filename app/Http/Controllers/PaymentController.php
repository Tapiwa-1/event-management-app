<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePaymentRequest;
use App\Models\Payment;
use App\Models\Quotation;
use Inertia\Inertia;
use Inertia\Response;

class PaymentController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Payments/Index', [
            'payments' => Payment::with('quotation.event.client')->latest('paid_at')->paginate(15),
            'quotations' => Quotation::with('event.client')->latest('issued_at')->get(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Payments/Create', [
            'quotations' => Quotation::with('event.client')->latest('issued_at')->get(),
        ]);
    }

    public function store(StorePaymentRequest $request)
    {
        Payment::create($request->validated());

        return redirect()->route('payments.index');
    }

    public function show(Payment $payment): Response
    {
        return Inertia::render('Payments/Show', [
            'payment' => $payment->load('quotation.event.client'),
        ]);
    }

    public function edit(Payment $payment): Response
    {
        return Inertia::render('Payments/Edit', [
            'payment' => $payment,
            'quotations' => Quotation::with('event.client')->latest('issued_at')->get(),
        ]);
    }

    public function update(StorePaymentRequest $request, Payment $payment)
    {
        $payment->update($request->validated());

        return redirect()->route('payments.index');
    }

    public function destroy(Payment $payment)
    {
        $payment->delete();

        return redirect()->route('payments.index');
    }
}
