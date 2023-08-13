<?php

namespace App\Http\Controllers;

use App\Http\Requests\TeamRequest;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware(['isAdminOrHasPermission']);
    }

    public function index()
    {
        $teams = Team::with('users')->withCount('users')->orderBy('name')->get();

        return Inertia::render('Admin/Dashboard', [
            'teamsProps' => $teams,
        ]);
    }

    public function createTeam(TeamRequest $request): \Illuminate\Http\JsonResponse
    {
        Team::create([
            'name' => $request->name,
            'departement' => $request->departement,
            'code_departement' => $request->code_departement,
            'company_id' => Auth::user()->company_id,
        ]);

        $teams = Team::withCount('users')->orderBy('name')->get();

        return response()->json($teams, 201);
    }
}
