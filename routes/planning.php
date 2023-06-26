<?php

use App\Http\Controllers\CalendarController;
use App\Http\Controllers\PlanningController;
use Illuminate\Support\Facades\Route;


Route::middleware('isCoordinateur')->group(function () {
    Route::post('/planning/generate', [PlanningController::class, 'generate'])->name('planning.generate');
    Route::patch('/planning/update', [PlanningController::class, 'update'])->name('planning.update');
    Route::patch('/planning/update/rotation', [PlanningController::class, 'updateWithRotation'])->name('planning.update.rotation');
});

Route::middleware('auth')->group(function () {

    Route::get('/planning', [CalendarController::class, 'getPlanning'])->name('planning');
    Route::post('/planning', [CalendarController::class, 'getPlanningCustom'])->name('planning.custom');
    Route::post('/planning/team', [PlanningController::class, 'getPlanningTeam'])->name('planning.team');
    Route::post('/planning/share', [PlanningController::class, 'generateShareLink'])->name('planning.share')->middleware('throttle:3,1');
});

Route::get('/planning/{token}', [PlanningController::class, 'getPlanningShare'])->name('planning.share.get')->middleware('throttle:3,1');
