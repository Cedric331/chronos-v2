<?php

use App\Http\Controllers\ExchangeRequestController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'exchange.enabled'])->group(function () {
    Route::get('/exchanges', [ExchangeRequestController::class, 'index'])->name('exchanges.index');
    Route::get('/exchanges/create', [ExchangeRequestController::class, 'create'])->name('exchanges.create');
    Route::post('/exchanges', [ExchangeRequestController::class, 'store'])->name('exchanges.store');
    Route::get('/exchanges/{exchange}', [ExchangeRequestController::class, 'show'])->name('exchanges.show');
    Route::post('/exchanges/{exchange}/accept', [ExchangeRequestController::class, 'accept'])->name('exchanges.accept');
    Route::post('/exchanges/{exchange}/reject', [ExchangeRequestController::class, 'reject'])->name('exchanges.reject');
    Route::post('/exchanges/{exchange}/cancel', [ExchangeRequestController::class, 'cancel'])->name('exchanges.cancel');
});
