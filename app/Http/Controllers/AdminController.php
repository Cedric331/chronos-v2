<?php

namespace App\Http\Controllers;

use App\Http\Requests\TeamRequest;
use App\Models\Ticket;
use App\Models\User;
use App\Repositories\TeamRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class AdminController extends Controller
{
    public function __construct(
        private TeamRepository $teamRepository,
        private UserRepository $userRepository
    ) {
        $this->middleware('isAdminOrHasPermission');
    }

    public function index()
    {
        $companyId = Auth::user()->company_id;

        $teams = $this->teamRepository->getTeamsByCompany(
            $companyId,
            ['users', 'coordinateur', 'params']
        )->loadCount('users');

        $coordinateursProps = $this->userRepository->getCoordinateursByCompany($companyId);

        // Statistiques supplémentaires
        $conseillersCount = User::whereHas('roles', function ($q) {
            $q->where('name', 'Conseiller');
        })->where('company_id', $companyId)->count();

        $responsablesCount = User::whereHas('roles', function ($q) {
            $q->where('name', 'Responsable');
        })->where('company_id', $companyId)->count();

        $usersActifsCount = User::where('company_id', $companyId)
            ->where('account_active', true)
            ->count();

        $totalUsersCount = User::where('company_id', $companyId)->count();

        $ticketsOuvertsCount = Ticket::whereHas('team', function ($q) use ($companyId) {
            $q->where('company_id', $companyId);
        })->open()->count();

        $ticketsTotalCount = Ticket::whereHas('team', function ($q) use ($companyId) {
            $q->where('company_id', $companyId);
        })->count();

        return Inertia::render('Admin/Dashboard', [
            'teamsProps' => $teams,
            'coordinateursProps' => $coordinateursProps,
            'stats' => [
                'conseillers' => $conseillersCount,
                'responsables' => $responsablesCount,
                'usersActifs' => $usersActifsCount,
                'totalUsers' => $totalUsersCount,
                'ticketsOuverts' => $ticketsOuvertsCount,
                'ticketsTotal' => $ticketsTotalCount,
            ],
        ]);
    }

    public function createTeam(TeamRequest $request): \Illuminate\Http\JsonResponse
    {
        $this->teamRepository->create([
            'name' => $request->name,
            'departement' => $request->departement,
            'code_departement' => $request->code_departement,
            'user_id' => $request->user_id,
            'company_id' => Auth::user()->company_id,
        ]);

        $teams = $this->teamRepository->getTeamsByCompany(Auth::user()->company_id)
            ->loadCount('users');

        return response()->json($teams, 201);
    }

    public function refreshTypeDays()
    {
        $teams = $this->teamRepository->getTeamsByCompany(Auth::user()->company_id, ['params']);

        foreach ($teams as $team) {
            if ($team->params) {
                $team->params->type_day = json_encode(config('teams.type_days_default'));
                $team->params->save();
            }
        }

        return response()->json($teams, 201);
    }
}
