<?php

use App\Http\Controllers\ContactController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//Route::get('/dashboard', function () {
//    return Inertia::render('Dashboard');
//})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::post('/contact', [ContactController::class, 'send'])->middleware('email.limit')->name('contact');

    Route::get('/privacy-policy', function () {
        return Inertia::render('PrivacyPolicy');
    })->name('privacy-policy');

    Route::get('/conditions', function () {
        return Inertia::render('Conditions');
    })->name('conditions');
});

require __DIR__.'/auth.php';
require __DIR__.'/calendar.php';
require __DIR__.'/event.php';
require __DIR__.'/team_params.php';
require __DIR__.'/rotation.php';
require __DIR__.'/team.php';
require __DIR__.'/admin.php';
require __DIR__.'/planning.php';
require __DIR__.'/statistique.php';
require __DIR__.'/paidleave.php';
require __DIR__.'/team_schedule.php';
