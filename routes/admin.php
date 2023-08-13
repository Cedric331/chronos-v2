<?php

use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.index')->middleware('isAdminOrHasPermission');
    Route::post('/admin/team/store', [AdminController::class, 'createTeam'])->name('admin.team.store')->middleware('isAdminOrHasPermission');

});
