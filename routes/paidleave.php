<?php

use App\Http\Controllers\PaidLeaveController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::get('/paidleave', [PaidLeaveController::class, 'view'])->name('paidleave.index');
    Route::post('/paidleave/store', [PaidLeaveController::class, 'store'])->name('paidleave.store');
    Route::post('/paidleave/search', [PaidLeaveController::class, 'search'])->name('paidleave.search');
    Route::get('/paidleave/statistics', [PaidLeaveController::class, 'statistics'])->name('paidleave.statistics');
    Route::middleware('isCoordinateur')->group(function () {
        Route::post('/paidleave/accepted', [PaidLeaveController::class, 'accepted'])->name('paidleave.accepted');
        Route::post('/paidleave/refused', [PaidLeaveController::class, 'refused'])->name('paidleave.refused');
        Route::delete('/paidleave/delete', [PaidLeaveController::class, 'delete'])->name('paidleave.delete');
    });
});
