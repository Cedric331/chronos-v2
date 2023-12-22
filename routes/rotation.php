<?php

use App\Http\Controllers\RotationController;
use Illuminate\Support\Facades\Route;

Route::middleware('isCoordinateur')->group(function () {
    Route::post('/team/rotation/{team}', [RotationController::class, 'store'])->name('team.post.rotation');
    Route::patch('/team/rotation/{rotation}', [RotationController::class, 'update'])->name('team.patch.rotation');
    Route::delete('/team/rotation/{rotation}', [RotationController::class, 'destroy'])->name('team.delete.rotation');
});
