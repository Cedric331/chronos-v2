<?php

namespace App\Http\Controllers;

use App\Exceptions\TeamException;
use App\Http\Requests\TeamRequest;
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
        $teams = $this->teamRepository->getTeamsByCompany(
            Auth::user()->company_id,
            ['users', 'coordinateur', 'params']
        )->loadCount('users');

        $coordinateursProps = $this->userRepository->getUsersByRole('Coordinateur');

        return Inertia::render('Admin/Dashboard', [
            'teamsProps' => $teams,
            'coordinateursProps' => $coordinateursProps,
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
