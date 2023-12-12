<?php

use App\Http\Controllers\PaidLeaveController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::get('/paidleave', [PaidLeaveController::class, 'view'])->name('paidleave.index');
    Route::post('/paidleave/store', [PaidLeaveController::class, 'store'])->name('paidleave.store');
    Route::post('/paidleave/accepted', [PaidLeaveController::class, 'accepted'])->name('paidleave.accepted');
    Route::post('/paidleave/refused', [PaidLeaveController::class, 'refused'])->name('paidleave.refused');

});
