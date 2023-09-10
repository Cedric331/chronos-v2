<?php

use App\Http\Controllers\StatistiqueController;
use Illuminate\Support\Facades\Route;

Route::middleware('isCoordinateur')->group(function () {
    Route::post('/statistics', [StatistiqueController::class, 'getStatistics'])->name('statistics');
});
