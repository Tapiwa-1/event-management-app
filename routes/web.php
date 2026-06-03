<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\PackageTemplateController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\QuotationController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ResourceController;
use App\Http\Controllers\ServiceController;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', DashboardController::class)->name('dashboard');

    Route::resource('clients', ClientController::class);
    Route::resource('events', EventController::class);
    Route::resource('services', ServiceController::class);
    Route::resource('quotations', QuotationController::class);
    Route::post('quotations/{quotation}/confirm', [QuotationController::class, 'confirm'])->name('quotations.confirm');
    Route::get('quotations/{quotation}/print', [QuotationController::class, 'printable'])->name('quotations.print');
    Route::resource('payments', PaymentController::class);
    Route::resource('resources', ResourceController::class);
    Route::post('resource-bookings', [ResourceController::class, 'book'])->name('resource-bookings.store');
    Route::patch('resource-bookings/{resourceBooking}/release', [ResourceController::class, 'release'])->name('resource-bookings.release');
    Route::resource('expenses', ExpenseController::class);
    Route::resource('packages', PackageTemplateController::class)->parameters(['packages' => 'package']);
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
});

require __DIR__.'/settings.php';
