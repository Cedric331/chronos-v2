<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketAttachment;
use App\Models\TicketCategory;
use App\Models\TicketComment;
use App\Models\TicketHistory;
use App\Mail\NewTicketNotification;
use App\Mail\TicketNotification;
use App\Models\TicketPriority;
use App\Models\TicketStatus;
use App\Models\TicketTag;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class TicketController extends Controller
{
    /**
     * Affiche la liste des tickets
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Ticket::with(['creator', 'assignee', 'status', 'category', 'priority', 'team', 'tags']);


        // Filtres supplémentaires
        if ($request->has('status') && $request->status) {
            if ($request->status === 'open') {
                $query->open();
            } elseif ($request->status === 'closed') {
                $query->closed();
            } else {
                $query->where('status_id', $request->status);
            }
        }

        if ($request->has('category') && $request->category) {
            $query->where('category_id', $request->category);
        }

        if ($request->has('priority') && $request->priority) {
            $query->where('priority_id', $request->priority);
        }

        if ($request->has('assigned_to') && $request->assigned_to) {
            if ($request->assigned_to === 'me') {
                $query->where('assigned_to', $user->id);
            } elseif ($request->assigned_to === 'unassigned') {
                $query->whereNull('assigned_to');
            } else {
                $query->where('assigned_to', $request->assigned_to);
            }
        }

        if ($request->has('created_by') && $request->created_by) {
            if ($request->created_by === 'me') {
                $query->where('created_by', $user->id);
            } else {
                $query->where('created_by', $request->created_by);
            }
        }

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('creator', function ($sq) use ($search) {
                      $sq->where('name', 'like', "%{$search}%");
                  })
                  ;
            });
        }

        // Tri
        $sortField = $request->input('sort_field', 'created_at');
        $sortDirection = $request->input('sort_direction', 'desc');

        $allowedSortFields = ['id', 'title', 'created_at', 'updated_at', 'due_date'];
        if (in_array($sortField, $allowedSortFields)) {
            $query->orderBy($sortField, $sortDirection);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $tickets = $query->paginate(10)->withQueryString();

        // Données pour les filtres
        $statuses = TicketStatus::orderBy('order')->get();
        $categories = TicketCategory::orderBy('name')->get();
        $priorities = TicketPriority::orderBy('level')->get();

        // Pas d'assignation d'utilisateurs

        return Inertia::render('Tickets/Index', [
            'tickets' => $tickets,
            'filters' => [
                'status' => $request->status,
                'category' => $request->category,
                'priority' => $request->priority,
                'assigned_to' => $request->assigned_to,
                'created_by' => $request->created_by,
                'search' => $request->search,
                'team_id' => $request->team_id,
                'sort_field' => $sortField,
                'sort_direction' => $sortDirection,
            ],
            'statuses' => $statuses,
            'categories' => $categories,
            'priorities' => $priorities,
            'can_manage' => $user->isAdministrateur(),
        ]);
    }

    /**
     * Affiche le formulaire de création d'un ticket
     */
    public function create()
    {
        $user = Auth::user();

        $statuses = TicketStatus::orderBy('order')->get();
        $categories = TicketCategory::orderBy('name')->get();
        $priorities = TicketPriority::orderBy('level')->get();
        $tags = TicketTag::orderBy('name')->get();

        // Pas d'assignation d'utilisateurs

        return Inertia::render('Tickets/Create', [
            'statuses' => $statuses,
            'categories' => $categories,
            'priorities' => $priorities,
            // Pas de tags ni d'assignation
            'can_manage' => $user->isAdministrateur(),
        ]);
    }

    /**
     * Enregistre un nouveau ticket
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:ticket_categories,id',
            'priority_id' => 'required|exists:ticket_priorities,id'
        ]);

        // Définir le statut par défaut (Nouveau)
        $statusId = TicketStatus::where('is_default', true)->first()->id;

        // Créer le ticket
        $ticket = Ticket::create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'status_id' => $statusId,
            'category_id' => $validated['category_id'],
            'priority_id' => $validated['priority_id'],
            'created_by' => $user->id,
            'team_id' => $user->team_id,
        ]);

        // Pas de tags ni de pièces jointes

        // Enregistrer l'historique
        TicketHistory::create([
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'field_name' => 'status',
            'new_value' => TicketStatus::find($statusId)->name,
        ]);

        // S'abonner automatiquement au ticket
        $ticket->subscribers()->attach($user->id);
        \Log::info('Utilisateur ' . $user->email . ' abonné au ticket #' . $ticket->id);

        // Enregistrer l'activité
        activity($user->team->name)
            ->event('Création')
            ->performedOn($ticket)
            ->log('Ticket #' . $ticket->id . ' créé : ' . $ticket->title);

        // Envoyer un email à tous les administrateurs
        $admins = User::whereHas('roles', function($query) {
            $query->where('name', 'Administrateur');
        })->get();

        if ($admins->count() > 0) {
            foreach ($admins as $admin) {
                try {
                    Mail::to($admin->email)->send(new NewTicketNotification($ticket, $user));
                    \Log::info('Email de notification de nouveau ticket envoyé à l\'administrateur ' . $admin->email);
                } catch (\Exception $e) {
                    \Log::error('Erreur lors de l\'envoi de l\'email de notification de nouveau ticket: ' . $e->getMessage());
                }
            }
        } else {
            \Log::warning('Aucun administrateur trouvé pour envoyer la notification de nouveau ticket');
        }

        return redirect()->route('tickets.show', $ticket->id)
            ->with('success', 'Ticket créé avec succès.');
    }

    /**
     * Affiche les détails d'un ticket
     */
    public function show(Ticket $ticket)
    {
        $user = Auth::user();

        // Vérifier si l'utilisateur a accès à ce ticket
        if (!$user->isAdmin() && $ticket->team_id !== $user->team_id) {
            abort(403, 'Vous n\'avez pas accès à ce ticket.');
        }

        $ticket->load([
            'creator',
            'assignee',
            'status',
            'category',
            'priority',
            'team',
            'tags',
            'comments' => function ($query) use ($user) {
                // Si l'utilisateur n'est pas admin, ne pas montrer les commentaires internes
                if (!$user->isAdministrateur()) {
                    $query->where('is_internal', false);
                }
                $query->with(['user', 'attachments']);
            },
            'attachments',
            'histories' => function ($query) {
                $query->with('user')->orderBy('created_at', 'desc');
            },
            'subscribers',
        ]);

        // Vérifier si l'utilisateur est abonné
        $isSubscribed = $ticket->subscribers->contains($user->id);

        // Données pour les formulaires
        $statuses = TicketStatus::orderBy('order')->get();
        $categories = TicketCategory::orderBy('name')->get();
        $priorities = TicketPriority::orderBy('level')->get();

        // Liste des utilisateurs pour l'assignation
        $users = [];
        if ($user->isAdministrateur()) {
            $users = User::orderBy('name')->get(['id', 'name', 'team_id']);
        } else {
            $users = User::where('team_id', $user->team_id)->orderBy('name')->get(['id', 'name']);
        }

        return Inertia::render('Tickets/Show', [
            'ticket' => $ticket,
            'isSubscribed' => $isSubscribed,
            'statuses' => $statuses,
            'categories' => $categories,
            'priorities' => $priorities,
            'users' => $users,
            'can_manage' => $user->isAdmin(),
        ]);
    }

    /**
     * Met à jour un ticket
     */
    public function update(Request $request, Ticket $ticket)
    {
        $user = Auth::user();

        // Vérifier si l'utilisateur a le droit de modifier ce ticket
        if (!$user->isAdmin() && $ticket->team_id !== $user->team_id) {
            abort(403, 'Vous n\'avez pas accès à ce ticket.');
        }

        // Les admins peuvent modifier tous les champs, y compris le statut
        if ($user->isAdministrateur()) {
            $validated = $request->validate([
                'title' => 'sometimes|required|string|max:255',
                'description' => 'sometimes|required|string',
                'status_id' => 'sometimes|required|exists:ticket_statuses,id',
                'category_id' => 'sometimes|required|exists:ticket_categories,id',
                'priority_id' => 'sometimes|required|exists:ticket_priorities,id',
                'due_date' => 'nullable|date',
            ]);
        }
        // Le créateur du ticket peut uniquement le clôturer (le mettre en Résolu ou Fermé)
        else if ($ticket->created_by === $user->id && $request->has('status_id')) {
            // Vérifier que le statut demandé est un statut de clôture (is_closed = true)
            $requestedStatus = TicketStatus::find($request->status_id);
            if (!$requestedStatus || !$requestedStatus->is_closed) {
                return redirect()->back()->with('error', 'Vous ne pouvez modifier le statut que pour clôturer le ticket.');
            }

            $validated = $request->validate([
                'title' => 'sometimes|required|string|max:255',
                'description' => 'sometimes|required|string',
                'status_id' => 'sometimes|required|exists:ticket_statuses,id',
                'category_id' => 'sometimes|required|exists:ticket_categories,id',
                'priority_id' => 'sometimes|required|exists:ticket_priorities,id',
            ]);
        }
        // Les autres utilisateurs peuvent modifier le titre, la description, la catégorie et la priorité
        // mais seulement si le ticket n'est pas clôturé
        else {
            // Vérifier si le ticket est clôturé
            if ($ticket->status->is_closed && $request->has('priority_id')) {
                return redirect()->back()->with('error', 'Impossible de modifier la priorité : ce ticket est clôturé.');
            }

            $validated = $request->validate([
                'title' => 'sometimes|required|string|max:255',
                'description' => 'sometimes|required|string',
                'category_id' => 'sometimes|required|exists:ticket_categories,id',
                'priority_id' => 'sometimes|required|exists:ticket_priorities,id',
            ]);
        }

        // Enregistrer l'historique des modifications
        foreach ($validated as $field => $value) {
            if ($field !== 'tags' && $ticket->{$field} != $value) {
                $oldValue = $ticket->{$field};
                $newValue = $value;

                // Formater les valeurs pour l'historique
                if (in_array($field, ['status_id', 'category_id', 'priority_id'])) {
                    $modelClass = [
                        'status_id' => TicketStatus::class,
                        'category_id' => TicketCategory::class,
                        'priority_id' => TicketPriority::class,
                    ][$field];

                    $oldValue = $oldValue ? $modelClass::find($oldValue)->name : null;
                    $newValue = $newValue ? $modelClass::find($newValue)->name : null;
                } elseif ($field === 'assigned_to') {
                    $oldValue = $oldValue ? User::find($oldValue)->name : 'Non assigné';
                    $newValue = $newValue ? User::find($newValue)->name : 'Non assigné';
                } elseif ($field === 'due_date') {
                    $oldValue = $oldValue ? Carbon::parse($oldValue)->format('d/m/Y') : null;
                    $newValue = $newValue ? Carbon::parse($newValue)->format('d/m/Y') : null;
                }

                // Créer l'entrée d'historique
                TicketHistory::create([
                    'ticket_id' => $ticket->id,
                    'user_id' => $user->id,
                    'field_name' => $field,
                    'old_value' => $oldValue,
                    'new_value' => $newValue,
                ]);
            }
        }

        // Mettre à jour le ticket
        $ticket->update($validated);

        // Vérifier si le ticket est fermé
        $newStatus = $ticket->status;
        if ($newStatus->is_closed && !$ticket->closed_at) {
            $ticket->closed_at = now();
            $ticket->save();

            TicketHistory::create([
                'ticket_id' => $ticket->id,
                'user_id' => $user->id,
                'field_name' => 'closed_at',
                'new_value' => now()->format('d/m/Y H:i'),
            ]);
        } elseif (!$newStatus->is_closed && $ticket->closed_at) {
            $ticket->closed_at = null;
            $ticket->save();

            TicketHistory::create([
                'ticket_id' => $ticket->id,
                'user_id' => $user->id,
                'field_name' => 'closed_at',
                'old_value' => Carbon::parse($ticket->closed_at)->format('d/m/Y H:i'),
                'new_value' => null,
            ]);
        }

        // Enregistrer l'activité
        activity($user->team->name)
            ->event('Mise à jour')
            ->performedOn($ticket)
            ->log('Ticket #' . $ticket->id . ' mis à jour');

        // Déterminer le type d'action pour la notification
        $action = 'update';
        if (isset($validated['status_id']) && $ticket->status->is_closed) {
            $action = 'closed';
        } elseif (isset($validated['status_id']) && !$ticket->status->is_closed && $ticket->closed_at) {
            $action = 'reopened';
        } elseif (isset($validated['status_id'])) {
            $action = 'status';
        }

        // Envoyer des notifications aux abonnés
        $subscribers = $ticket->subscribers()->where('users.id', '!=', $user->id)->get();

        // Log pour débogage
        \Log::info('Envoi de notifications pour le ticket #' . $ticket->id);
        \Log::info('Nombre d\'abonnés: ' . $subscribers->count());

        if ($subscribers->count() > 0) {
            foreach ($subscribers as $subscriber) {
                \Log::info('Envoi de notification à ' . $subscriber->email);
                try {
                    Mail::to($subscriber->email)->send(new TicketNotification($ticket, $action, $user));
                    \Log::info('Email envoyé avec succès à ' . $subscriber->email);
                } catch (\Exception $e) {
                    \Log::error('Erreur lors de l\'envoi de l\'email: ' . $e->getMessage());
                }
            }
        } else {
            \Log::info('Aucun abonné à notifier');
        }

        return redirect()->route('tickets.show', $ticket->id)
            ->with('success', 'Ticket mis à jour avec succès.');
    }

    /**
     * Ajoute un commentaire à un ticket
     */
    public function addComment(Request $request, Ticket $ticket): \Illuminate\Http\RedirectResponse
    {
        $user = Auth::user();

        // Vérifier si l'utilisateur a accès à ce ticket
        if (!$user->isAdmin() && $ticket->team_id !== $user->team_id) {
            abort(403, 'Vous n\'avez pas accès à ce ticket.');
        }

        // Vérifier si le ticket est fermé ou résolu
        if ($ticket->status->is_closed) {
            return redirect()->back()->with('error', 'Impossible d\'ajouter un commentaire : ce ticket est clôturé.');
        }

        $validated = $request->validate([
            'content' => 'required|string',
            'is_internal' => 'boolean',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|max:10240', // 10MB max
        ]);

        // Seuls les admins peuvent créer des commentaires internes
        $isInternal = $user->isAdmin() && isset($validated['is_internal']) && $validated['is_internal'];

        // Créer le commentaire
        $comment = TicketComment::create([
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'content' => $validated['content'],
            'is_internal' => $isInternal,
        ]);

        // Gérer les pièces jointes
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('ticket-attachments');

                TicketAttachment::create([
                    'ticket_id' => $ticket->id,
                    'comment_id' => $comment->id,
                    'user_id' => $user->id,
                    'filename' => $file->getClientOriginalName(),
                    'path' => $path,
                    'mime_type' => $file->getMimeType(),
                    'size' => $file->getSize(),
                ]);
            }
        }

        // S'abonner automatiquement au ticket s'il ne l'est pas déjà
        if (!$ticket->subscribers->contains($user->id)) {
            $ticket->subscribers()->attach($user->id);
            \Log::info('Utilisateur ' . $user->email . ' abonné au ticket #' . $ticket->id . ' après commentaire');
        } else {
            \Log::info('Utilisateur ' . $user->email . ' déjà abonné au ticket #' . $ticket->id);
        }

        // Enregistrer l'activité
        activity($user->team->name)
            ->event('Commentaire')
            ->performedOn($ticket)
            ->log('Commentaire ajouté au ticket #' . $ticket->id);

        // Envoyer des notifications aux abonnés (sauf pour les commentaires internes)
        if (!$isInternal) {
            $subscribers = $ticket->subscribers()->where('users.id', '!=', $user->id)->get();

            // Log pour débogage
            \Log::info('Envoi de notifications de commentaire pour le ticket #' . $ticket->id);
            \Log::info('Nombre d\'abonnés: ' . $subscribers->count());

            if ($subscribers->count() > 0) {
                foreach ($subscribers as $subscriber) {
                    \Log::info('Envoi de notification de commentaire à ' . $subscriber->email);
                    try {
                        Mail::to($subscriber->email)->send(new TicketNotification($ticket, 'comment', $user, $comment));
                        \Log::info('Email de commentaire envoyé avec succès à ' . $subscriber->email);
                    } catch (\Exception $e) {
                        \Log::error('Erreur lors de l\'envoi de la notification de commentaire: ' . $e->getMessage());
                    }
                }
            } else {
                \Log::info('Aucun abonné à notifier pour le commentaire');
            }
        } else {
            \Log::info('Commentaire interne - pas de notification envoyée');
        }

        return redirect()->route('tickets.show', $ticket->id)
            ->with('success', 'Commentaire ajouté avec succès.');
    }

    /**
     * S'abonner ou se désabonner d'un ticket
     */
    public function toggleSubscription(Ticket $ticket)
    {
        $user = Auth::user();

        // Vérifier si l'utilisateur a accès à ce ticket
        if (!$user->isAdmin() && $ticket->team_id !== $user->team_id) {
            abort(403, 'Vous n\'avez pas accès à ce ticket.');
        }

        // Vérifier si le ticket est fermé ou résolu
        if ($ticket->status->is_closed) {
            return redirect()->back()->with('error', 'Impossible de s\'abonner/désabonner : ce ticket est clôturé.');
        }

        // Vérifier si l'utilisateur est déjà abonné
        $isSubscribed = $ticket->subscribers->contains($user->id);
        \Log::info('Statut d\'abonnement actuel de ' . $user->email . ' au ticket #' . $ticket->id . ': ' . ($isSubscribed ? 'Abonné' : 'Non abonné'));

        if ($isSubscribed) {
            $ticket->subscribers()->detach($user->id);
            $message = 'Vous vous êtes désabonné du ticket.';
            \Log::info('Utilisateur ' . $user->email . ' désabonné du ticket #' . $ticket->id);
        } else {
            $ticket->subscribers()->attach($user->id);
            $message = 'Vous vous êtes abonné au ticket.';
            \Log::info('Utilisateur ' . $user->email . ' abonné au ticket #' . $ticket->id . ' via toggleSubscription');
        }

        return redirect()->route('tickets.show', $ticket->id)
            ->with('success', $message);
    }

    /**
     * Télécharger une pièce jointe
     */
    public function downloadAttachment(TicketAttachment $attachment)
    {
        $user = Auth::user();
        $ticket = $attachment->ticket;

        // Vérifier si l'utilisateur a accès à ce ticket
        if (!$user->isAdmin() && $ticket->team_id !== $user->team_id) {
            abort(403, 'Vous n\'avez pas accès à cette pièce jointe.');
        }

        // Vérifier si le commentaire est interne et si l'utilisateur n'est pas admin
        if ($attachment->comment && $attachment->comment->is_internal && !$user->isAdmin()) {
            abort(403, 'Vous n\'avez pas accès à cette pièce jointe.');
        }

        return Storage::download($attachment->path, $attachment->filename);
    }
}
