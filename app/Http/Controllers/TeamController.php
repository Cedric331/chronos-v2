<?php

namespace App\Http\Controllers;

use App\Http\Requests\TeamRequest;
use App\Models\LinkTeam;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class TeamController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show($name): \Inertia\Response|\Symfony\Component\HttpFoundation\Response
    {
        $team = Team::with(['rotations.details', 'params'])
            ->where('name', $name)
            ->firstOrFail();

        if (!Auth::user()->team || Auth::user()->team->id !== $team->id || !config('teams.active')) {
            return Inertia::location('/');
        }

        $teamSchedules = null;
        if ($team->moduleAlertActive()) {
            $teamSchedules = $team->teamSchedules;
        }

        $teamWithUsers = $team->load('users');

        return Inertia::render('Team/Team', [
            'team' => $teamWithUsers,
            'schedules' => $teamSchedules
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Team $team)
    {
        //
    }


    public function update(TeamRequest $request, Team $team): \Illuminate\Http\JsonResponse|\Inertia\Response
    {
        if (!$request->ajax()) {
            return Inertia::render('Errors/404');
        }

        $data = $request->validated();

        if ($request->hasFile('logo')) {
            $logo = $request->file('logo');

            // Supprime l'ancien logo si nécessaire
            if ($team->logo) {
                Storage::disk('public')->delete($team->logo);
            }

            // Crée le dossier portant le nom de l'équipe s'il n'existe pas
            $teamFolder = 'teams/' . $team->name;
            if (!Storage::disk('public')->exists($teamFolder)) {
                Storage::disk('public')->makeDirectory($teamFolder);
            }

            // Enregistre le nouveau logo dans le dossier portant le nom de l'équipe
            $logoPath = $logo->storeAs($teamFolder, $logo->getClientOriginalName(), 'public');
            $data['logo'] = $logoPath;
        }

        $team->update($data);


        return response()->json(['url' => route('team.show', ['name' => $team->name])]);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Team $team)
    {
        //
    }

    /**
     * @param Request $request
     * @param Team $team
     */
    public function deleteLogo(Request $request,Team $team)
    {
        if (!$request->ajax()) {
            return Inertia::render('Errors/404');
        }
        // Supprimer le fichier du système de fichiers
        if (Storage::disk('public')->exists($team->logo)) {
            Storage::disk('public')->delete($team->logo);
        }

        // Mettre à jour la colonne logo dans la base de données
        $team->update(['logo' => null]);
    }

    public function switch (Team $team): \Illuminate\Http\JsonResponse|\Inertia\Response
    {
        if (!Gate::check('has-role-coordinateur') || !config('teams.active')) {
            return Inertia::render('Errors/401');
        }

        Auth::user()->update(['team_id' => $team->id]);

        return response()->json(['url' => route('team.show', ['name' => $team->name])]);
    }

    public function getInformation (Request $request)
    {

        $team = Auth::user()->team;

        if (!config('teams.active') || !$team || $request->ajax()) {
            return Inertia::render('Errors/404');
        }

        $users = User::where('team_id', $team->id)->select('id', 'name', 'phone', 'email', 'birthday', 'account_active')->get();
        $links = null;

        if ($team->params->share_link) {
            $links = LinkTeam::where('team_id', $team->id)
                ->with(['user' => function($query) {
                    $query->select('id', 'name');
                }])
                ->get();
        }

        return Inertia::render('Information/InformationTeam', [
            'usersProps' => $users,
            'linksProps' => $links
        ]);
    }
}
