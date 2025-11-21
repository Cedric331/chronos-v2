<?php

namespace App\Http\Controllers;

use App\Enums\ExchangeRequestStatus;
use App\Exceptions\ExchangeException;
use App\Mail\ExchangeRequestAccepted;
use App\Mail\ExchangeRequestCreated;
use App\Models\ExchangeRequest;
use App\Models\Planning;
use App\Repositories\UserRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;

class ExchangeRequestController extends Controller
{
    public function __construct(
        private UserRepository $userRepository
    ) {}

    /**
     * Affiche la liste des demandes d'échange pour l'utilisateur connecté
     */
    public function index()
    {
        $user = Auth::user();
        $team = $user->team;

        // Récupérer les demandes d'échange envoyées par l'utilisateur
        $sentRequests = ExchangeRequest::with(['requesterPlanning.calendar', 'requestedPlanning.calendar', 'requested'])
            ->where('requester_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        // Récupérer les demandes d'échange reçues par l'utilisateur
        $receivedRequests = ExchangeRequest::with(['requesterPlanning.calendar', 'requestedPlanning.calendar', 'requester'])
            ->where('requested_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('Exchange/Index', [
            'sentRequests' => $sentRequests,
            'receivedRequests' => $receivedRequests,
            'isCoordinateur' => $user->isCoordinateur(),
        ]);
    }

    /**
     * Affiche le formulaire pour créer une nouvelle demande d'échange
     */
    public function create()
    {
        $user = Auth::user();
        $team = $user->team;

        // Récupérer les utilisateurs de l'équipe (sauf l'utilisateur connecté)
        $teamUsers = User::where('team_id', $team->id)
            ->where('id', '!=', $user->id)
            ->get();

        // Récupérer les plannings de l'utilisateur connecté pour les 30 prochains jours
        $userPlannings = Planning::with('calendar')
            ->where('user_id', $user->id)
            ->whereHas('calendar', function ($query) {
                $query->where('date', '>=', Carbon::now()->startOfDay())
                    ->where('date', '<=', Carbon::now()->addDays(30)->endOfDay());
            })
            ->get();

        return Inertia::render('Exchange/Create', [
            'teamUsers' => $teamUsers,
            'userPlannings' => $userPlannings,
        ]);
    }

    /**
     * Enregistre une nouvelle demande d'échange
     */
    public function store(Request $request)
    {
        $request->validate([
            'requested_id' => 'required|exists:users,id',
            'exchanges' => 'required|array|min:1',
            'exchanges.*.requester_planning_id' => 'required|exists:plannings,id',
            'exchanges.*.requested_planning_id' => 'required|exists:plannings,id',
            'requester_comment' => 'nullable|string|max:500',
        ]);

        $user = Auth::user();
        $team = $user->team;
        $exchanges = $request->exchanges;
        $requestedId = $request->requested_id;

        // Vérifier que tous les plannings appartiennent aux bons utilisateurs
        foreach ($exchanges as $exchange) {
            $requesterPlanning = Planning::findOrFail($exchange['requester_planning_id']);
            $requestedPlanning = Planning::findOrFail($exchange['requested_planning_id']);

            if ($requesterPlanning->user_id !== $user->id) {
                throw ExchangeException::planningNotOwned();
            }

            if ($requestedPlanning->user_id !== $requestedId) {
                throw ExchangeException::planningNotOwnedByUser($requestedId);
            }

            // Vérifier qu'il n'y a pas déjà une demande en cours pour ces plannings
            $existingRequest = ExchangeRequest::where(function ($query) use ($exchange) {
                $query->where('requester_planning_id', $exchange['requester_planning_id'])
                    ->orWhere('requested_planning_id', $exchange['requested_planning_id']);
            })
                ->where('status', ExchangeRequestStatus::PENDING->value)
                ->first();

            if ($existingRequest) {
                throw ExchangeException::exchangeRequestAlreadyExists();
            }
        }

        DB::beginTransaction();
        try {
            // Créer les demandes d'échange pour chaque paire de plannings
            $exchangeRequests = [];

            foreach ($exchanges as $exchange) {
                $exchangeRequest = ExchangeRequest::create([
                    'requester_id' => $user->id,
                    'requested_id' => $requestedId,
                    'requester_planning_id' => $exchange['requester_planning_id'],
                    'requested_planning_id' => $exchange['requested_planning_id'],
                    'team_id' => $team->id,
                    'requester_comment' => $request->requester_comment,
                    'status' => ExchangeRequestStatus::PENDING->value,
                ]);

                $exchangeRequests[] = $exchangeRequest;
            }

            // Journaliser l'action
            activity($team->name)
                ->event('Demande d\'\u00e9change multiple')
                ->log('Une demande d\'\u00e9change pour '.count($exchanges).' jour(s) a été créée');

            // Envoyer une notification par email au coordinateur de l'équipe
            $coordinateur = $team->coordinateur;
            if ($coordinateur && count($exchangeRequests) > 0) {
                // Charger les relations nécessaires pour l'email pour tous les échanges
                foreach ($exchangeRequests as $exchangeRequest) {
                    $exchangeRequest->load(['requester', 'requested', 'requesterPlanning.calendar', 'requestedPlanning.calendar']);
                }

                try {
                    // Générer l'URL pour voir la liste des échanges
                    $url = route('exchanges.index');

                    // Envoyer un seul email avec tous les échanges
                    Mail::to($coordinateur->email)->send(new ExchangeRequestCreated($exchangeRequests, $url));
                    // Envoi du mail à l'utilisateur demandé
                    $requestedUser = $this->userRepository->find($requestedId);
                    if ($requestedUser) {
                        Mail::to($requestedUser->email)->send(new ExchangeRequestCreated($exchangeRequests, $url));
                    }
                } catch (\Exception $e) {
                    \Log::error('Erreur lors de l\'envoi de l\'email de notification de demande d\'échange: '.$e->getMessage());
                }
            }

            DB::commit();

            return redirect()->route('exchanges.index')
                ->with('success', 'Votre demande d\'\u00e9change pour '.count($exchanges).' jour(s) a été envoyée avec succès.');
        } catch (\Exception $e) {
            DB::rollback();

            return back()->withErrors(['general' => 'Une erreur est survenue lors de la création de la demande d\'\u00e9change: '.$e->getMessage()]);
        }
    }

    /**
     * Affiche les détails d'une demande d'échange
     */
    public function show(ExchangeRequest $exchange)
    {
        $user = Auth::user();

        // Vérifier que l'utilisateur a le droit de voir cette demande
        if ($user->id !== $exchange->requester_id && $user->id !== $exchange->requested_id && ! $user->isCoordinateur()) {
            abort(403, 'Vous n\'avez pas le droit de voir cette demande d\'\u00e9change.');
        }

        $exchange->load(['requester', 'requested', 'requesterPlanning.calendar', 'requestedPlanning.calendar']);

        return Inertia::render('Exchange/Show', [
            'exchange' => $exchange,
            'isCoordinateur' => $user->isCoordinateur(),
        ]);
    }

    /**
     * Accepte une demande d'échange (par l'utilisateur demandé)
     */
    public function accept(ExchangeRequest $exchange, Request $request)
    {
        $request->validate([
            'requested_comment' => 'nullable|string|max:500',
        ]);

        $user = Auth::user();

        // Vérifier que l'utilisateur est bien le destinataire de la demande
        if ($user->id !== $exchange->requested_id) {
            abort(403, 'Vous n\'avez pas le droit d\'accepter cette demande d\'\u00e9change.');
        }

        // Vérifier que la demande est en attente
        if ($exchange->status !== ExchangeRequestStatus::PENDING->value) {
            return back()->withErrors(['general' => 'Cette demande d\'\u00e9change ne peut plus être acceptée.']);
        }

        DB::beginTransaction();
        try {
            // Mettre à jour la demande
            $exchange->update([
                'status' => ExchangeRequestStatus::ACCEPTED->value,
                'requested_comment' => $request->requested_comment,
                'responded_at' => now(),
            ]);

            // Effectuer l'échange des plannings immédiatement
            $this->processExchange($exchange);

            // Journaliser l'action
            activity($user->team->name)
                ->event('Acceptation d\'\u00e9change')
                ->performedOn($exchange)
                ->log('Une demande d\'\u00e9change a été acceptée et les plannings ont été échangés');

            // Envoyer une notification par email au coordinateur de l'équipe
            $team = $user->team;
            $coordinateur = $team->coordinateur;
            if ($coordinateur) {
                // Charger les relations nécessaires pour l'email
                $exchange->load(['requester', 'requested', 'requesterPlanning.calendar', 'requestedPlanning.calendar']);

                try {
                    Mail::to($coordinateur->email)->send(new ExchangeRequestAccepted($exchange));
                    // Envoi du mail à l'utilisateur à l'origine de la demande
                    Mail::to(User::find($exchange->requester_id)->email)->send(new ExchangeRequestAccepted($exchange));
                } catch (\Exception $e) {
                    \Log::error('Erreur lors de l\'envoi de l\'email de notification d\'acceptation d\'échange: '.$e->getMessage());
                }
            }

            DB::commit();

            return redirect()->route('exchanges.index')
                ->with('success', 'Vous avez accepté la demande d\'\u00e9change avec succès et les plannings ont été échangés.');
        } catch (\Exception $e) {
            DB::rollback();

            return back()->withErrors(['general' => 'Une erreur est survenue lors de l\'acceptation de la demande d\'\u00e9change.']);
        }
    }

    /**
     * Rejette une demande d'échange (par l'utilisateur demandé)
     */
    public function reject(ExchangeRequest $exchange, Request $request)
    {
        $request->validate([
            'requested_comment' => 'nullable|string|max:500',
        ]);

        $user = Auth::user();

        // Vérifier que l'utilisateur est bien le destinataire de la demande
        if ($user->id !== $exchange->requested_id) {
            abort(403, 'Vous n\'avez pas le droit de rejeter cette demande d\'\u00e9change.');
        }

        // Vérifier que la demande est en attente
        if ($exchange->status !== ExchangeRequestStatus::PENDING->value) {
            return back()->withErrors(['general' => 'Cette demande d\'\u00e9change ne peut plus être rejetée.']);
        }

        DB::beginTransaction();
        try {
            // Mettre à jour la demande
            $exchange->update([
                'status' => ExchangeRequestStatus::REJECTED->value,
                'requested_comment' => $request->requested_comment,
                'responded_at' => now(),
            ]);

            // Journaliser l'action
            activity($user->team->name)
                ->event('Rejet d\'\u00e9change')
                ->performedOn($exchange)
                ->log('Une demande d\'\u00e9change a été rejetée');

            DB::commit();

            return redirect()->route('exchanges.index')
                ->with('success', 'Vous avez rejeté la demande d\'\u00e9change.');
        } catch (\Exception $e) {
            DB::rollback();

            return back()->withErrors(['general' => 'Une erreur est survenue lors du rejet de la demande d\'\u00e9change.']);
        }
    }

    /**
     * Annule une demande d'échange (par l'utilisateur demandeur)
     */
    public function cancel(ExchangeRequest $exchange)
    {
        $user = Auth::user();

        // Vérifier que l'utilisateur est bien l'auteur de la demande
        if ($user->id !== $exchange->requester_id) {
            abort(403, 'Vous n\'avez pas le droit d\'annuler cette demande d\'\u00e9change.');
        }

        // Vérifier que la demande est en attente
        if ($exchange->status !== ExchangeRequestStatus::PENDING->value) {
            return back()->withErrors(['general' => 'Cette demande d\'\u00e9change ne peut plus être annulée.']);
        }

        DB::beginTransaction();
        try {
            // Mettre à jour la demande
            $exchange->update([
                'status' => ExchangeRequestStatus::CANCELLED->value,
                'responded_at' => now(),
            ]);

            // Journaliser l'action
            activity($user->team->name)
                ->event('Annulation d\'\u00e9change')
                ->performedOn($exchange)
                ->log('Une demande d\'\u00e9change a été annulée');

            DB::commit();

            return redirect()->route('exchanges.index')
                ->with('success', 'Vous avez annulé la demande d\'\u00e9change.');
        } catch (\Exception $e) {
            DB::rollback();

            return back()->withErrors(['general' => 'Une erreur est survenue lors de l\'annulation de la demande d\'\u00e9change.']);
        }
    }

    /**
     * Effectue l'échange des plannings
     */
    private function processExchange(ExchangeRequest $exchange)
    {
        // Récupérer les plannings
        $requesterPlanning = Planning::findOrFail($exchange->requester_planning_id);
        $requestedPlanning = Planning::findOrFail($exchange->requested_planning_id);

        // Récupérer les utilisateurs
        $requesterId = $exchange->requester_id;
        $requestedId = $exchange->requested_id;

        // Sauvegarder les données temporairement
        $requesterData = [
            'user_id' => $requestedId, // Changer l'utilisateur
            'type_day' => $requesterPlanning->type_day,
            'debut_journee' => $requesterPlanning->debut_journee,
            'debut_pause' => $requesterPlanning->debut_pause,
            'fin_pause' => $requesterPlanning->fin_pause,
            'fin_journee' => $requesterPlanning->fin_journee,
            'is_technician' => $requesterPlanning->is_technician,
            'telework' => $requesterPlanning->telework,
            'hours' => $requesterPlanning->hours,
            'rotation_id' => $requesterPlanning->rotation_id,
        ];

        $requestedData = [
            'user_id' => $requesterId, // Changer l'utilisateur
            'type_day' => $requestedPlanning->type_day,
            'debut_journee' => $requestedPlanning->debut_journee,
            'debut_pause' => $requestedPlanning->debut_pause,
            'fin_pause' => $requestedPlanning->fin_pause,
            'fin_journee' => $requestedPlanning->fin_journee,
            'is_technician' => $requestedPlanning->is_technician,
            'telework' => $requestedPlanning->telework,
            'hours' => $requestedPlanning->hours,
            'rotation_id' => $requestedPlanning->rotation_id,
        ];

        // Échanger les données et les utilisateurs
        $requesterPlanning->update($requestedData);
        $requestedPlanning->update($requesterData);

        // Journaliser l'action
        activity(Auth::user()->team->name)
            ->event('Échange de planning')
            ->log('Un échange de planning a été effectué entre '.User::find($requesterId)->name.' et '.User::find($requestedId)->name);

        return true;
    }
}
