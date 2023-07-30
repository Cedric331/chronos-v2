<?php

use App\Http\Controllers\EventPlanningController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {

    Route::post('/event', [EventPlanningController::class, 'store'])->name('event.post')->middleware('isCoordinateur');
    Route::delete('/event', [EventPlanningController::class, 'delete'])->name('event.delete')->middleware('isCoordinateur');
});
