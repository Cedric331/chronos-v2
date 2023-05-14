<?php

use App\Http\Controllers\TeamController;
use App\Http\Controllers\TeamParamsController;
use Illuminate\Support\Facades\Route;


Route::middleware('isCoordinateur')->group(function () {
    Route::patch('/team/params/update/{teamParams}', [TeamParamsController::class, 'update'])->name('teamParams.update');
});
