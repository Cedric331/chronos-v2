<?php

use App\Http\Controllers\TicketController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    // Routes accessibles à tous les utilisateurs authentifiés
    Route::get('/tickets', [TicketController::class, 'index'])->name('tickets.index');
    Route::get('/tickets/create', [TicketController::class, 'create'])->name('tickets.create');
    Route::post('/tickets', [TicketController::class, 'store'])->name('tickets.store');
    Route::get('/tickets/{ticket}', [TicketController::class, 'show'])->name('tickets.show');

    // Commentaires et abonnements
    Route::post('/tickets/{ticket}/comments', [TicketController::class, 'addComment'])->name('tickets.comments.store');
    Route::post('/tickets/{ticket}/toggle-subscription', [TicketController::class, 'toggleSubscription'])->name('tickets.toggle-subscription');

    // Téléchargement des pièces jointes
    Route::get('/attachments/{attachment}/download', [TicketController::class, 'downloadAttachment'])->name('attachments.download');

    // Mise à jour des tickets (accessible à tous, mais avec des restrictions dans le contrôleur)
    Route::put('/tickets/{ticket}', [TicketController::class, 'update'])->name('tickets.update');
});
