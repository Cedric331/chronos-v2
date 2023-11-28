<?php

use App\Http\Controllers\PaidLeaveController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::get('/paidleave', [PaidLeaveController::class, 'view'])->name('paidleave.index');
    Route::post('/paidleave/store', [PaidLeaveController::class, 'store'])->name('paidleave.store');
});
