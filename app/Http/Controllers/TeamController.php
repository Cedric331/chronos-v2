<?php

namespace App\Http\Controllers;

use App\Http\Requests\TeamRequest;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

        $teamWithUsers = $team->load('users');

        return Inertia::render('Team/Team', [
            'team' => $teamWithUsers
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Team $team)
    {
        //
    }



    public function update(TeamRequest $request, Team $team): \Illuminate\Http\JsonResponse
    {
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
     * @param Team $team
     * @return void
     */
    public function deleteLogo(Team $team): void
    {
        // Supprimer le fichier du système de fichiers
        if (Storage::disk('public')->exists($team->logo)) {
            Storage::disk('public')->delete($team->logo);
        }


        // Mettre à jour la colonne logo dans la base de données
        $team->update(['logo' => null]);
    }
}
