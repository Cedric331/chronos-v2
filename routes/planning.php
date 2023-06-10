<?php

use App\Http\Controllers\PlanningController;
use Illuminate\Support\Facades\Route;


Route::middleware('isCoordinateur')->group(function () {
    Route::post('/planning/generate', [PlanningController::class, 'generate'])->name('planning.generate');
    Route::patch('/planning/update', [PlanningController::class, 'update'])->name('planning.update');
});
