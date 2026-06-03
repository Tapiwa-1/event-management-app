<?php

use App\Http\Controllers\Api\EventManagementApiController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    Route::get('mobile/bootstrap', [EventManagementApiController::class, 'bootstrap']);
    Route::get('dashboard', [EventManagementApiController::class, 'dashboard']);
    Route::get('reports', [EventManagementApiController::class, 'reports']);

    Route::get('clients', [EventManagementApiController::class, 'clients']);
    Route::post('clients', [EventManagementApiController::class, 'storeClient']);
    Route::patch('clients/{client}', [EventManagementApiController::class, 'updateClient']);

    Route::get('events', [EventManagementApiController::class, 'events']);
    Route::post('events', [EventManagementApiController::class, 'storeEvent']);
    Route::patch('events/{event}', [EventManagementApiController::class, 'updateEvent']);

    Route::get('services', [EventManagementApiController::class, 'services']);
    Route::post('services', [EventManagementApiController::class, 'storeService']);

    Route::get('packages', [EventManagementApiController::class, 'packages']);
    Route::post('packages', [EventManagementApiController::class, 'storePackage']);

    Route::get('quotations', [EventManagementApiController::class, 'quotations']);
    Route::get('quotations/{quotation}', [EventManagementApiController::class, 'quotation']);
    Route::post('quotations', [EventManagementApiController::class, 'storeQuotation']);
    Route::post('quotations/{quotation}/confirm', [EventManagementApiController::class, 'confirmQuotation']);

    Route::get('payments', [EventManagementApiController::class, 'payments']);
    Route::post('payments', [EventManagementApiController::class, 'storePayment']);

    Route::get('resources', [EventManagementApiController::class, 'resources']);
    Route::post('resources', [EventManagementApiController::class, 'storeResource']);
    Route::post('resource-bookings', [EventManagementApiController::class, 'bookResource']);

    Route::get('expenses', [EventManagementApiController::class, 'expenses']);
    Route::post('expenses', [EventManagementApiController::class, 'storeExpense']);
});
