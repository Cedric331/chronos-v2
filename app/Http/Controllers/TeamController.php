<?php

namespace App\Http\Controllers;

use App\Http\Requests\TeamRequest;
use App\Models\LinkTeam;
use App\Models\Team;
use App\Models\User;
use ColorThief\ColorThief;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Spatie\Activitylog\Models\Activity;

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
        $team = Team::with(['rotations.details', 'params', 'coordinateur'])
            ->where('name', $name)
            ->firstOrFail();

        if (! Auth::user()->team || Auth::user()->team->id !== $team->id || ! config('teams.active')) {
            return Inertia::location('/');
        }

        $teamSchedules = null;
        if ($team->moduleAlertActive()) {
            $teamSchedules = $team->teamSchedules;
        }

        $roleNames = ['Conseiller'];
        if ($team->params->send_coordinateur) {
            $roleNames[] = 'Coordinateur';
        }
        $teamWithUsers = $team->load(['users' => function ($query) use ($roleNames, $team) {
            $query->whereHas('roles', function ($roleQuery) use ($roleNames) {
                $roleQuery->whereIn('name', $roleNames);
            });
        }]);

        $filteredUsers = $teamWithUsers->users->reject(function ($item) use ($team) {
            return $item->isCoordinateur() && $team->user_id !== $item->id;
        });

        $teamWithUsers->setRelation('users', $filteredUsers);


        $coordinateursProps = User::whereHas('roles', function ($query) {
            $query->where('name', 'Coordinateur');
        })->get();

        return Inertia::render('Team/Team', [
            'team' => $teamWithUsers,
            'schedules' => $teamSchedules,
            'coordinateursProps' => $coordinateursProps,
        ]);
    }

    public function paginateActivities(Request $request): \Illuminate\Http\JsonResponse|\Inertia\Response
    {
        $page = $request->input('page', 1);

        if (!$request->team_id && Auth::user()->isAdmin()) {
            $activities = Activity::with(['subject', 'causer'])
                ->paginate(10, ['*'], 'page', $page);
        } else {
            if ($request->team_id == Auth::user()->team_id) {
                $team = Team::findOrFail($request->team_id);
                $activities = Activity::where('log_name', $team->name)
                    ->with(['subject', 'causer'])
                    ->paginate(10, ['*'], 'page', $page);
            } else {
                return Inertia::render('Errors/404');
            }
        }

        $activities->getCollection()->transform(function ($activity) {
            $activity->subject_type = class_basename($activity->subject_type) === 'User' ? 'Utilisateur' : class_basename($activity->subject_type);
            $activity->formatted_date = $activity->created_at->format('d/m/Y H:i');
            return $activity;
        });

        return response()->json($activities);
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
        if (! $request->ajax()) {
            return Inertia::render('Errors/404');
        }

        $previousUrl = url()->previous();
        $previousRoute = app('router')->getRoutes()->match(app('request')->create($previousUrl));
        $previousRouteName = $previousRoute->getName();

        $data = $request->validated();

        $logoPath = null;
        if ($request->hasFile('logo')) {
            $logo = $request->file('logo');

            if ($team->logo) {
                Storage::disk('public')->delete($team->logo);
            }

            $teamFolder = 'teams/'.$team->name;
            if (! Storage::disk('public')->exists($teamFolder)) {
                Storage::disk('public')->makeDirectory($teamFolder);
            }

            $logoPath = $logo->storeAs($teamFolder, $logo->getClientOriginalName(), 'public');
            $data['logo'] = $logoPath;
        }

        $activities = Activity::where('log_name', $team->name)->get();

        $update = $team->update($data);

        if ($update) {
            foreach ($activities as $activity) {
                $activity->log_name = $team->name;
                $activity->save();
            }
        }

        if ($logoPath && $request->checked) {
            $colors = ColorThief::getPalette(Storage::disk('public')->path($logoPath), 8);
            $this->getColor($colors, $team);
        }

        if ($previousRouteName === 'admin.index') {
            return response()->json($team->name);
        } else {
            return response()->json(['url' => route('team.show', ['name' => $team->name])]);
        }

    }

    private function getColor ($colors, $team)
    {
        usort($colors, function($a, $b) {
            return array_sum($b) - array_sum($a);
        });

        $data['color1'] = $this->rgbToHex($colors[0]);
        $data['color2'] = $this->rgbToHex($colors[1]);
        $data['color3'] = $this->rgbToHex($colors[2]);
        $data['color4'] = $this->rgbToHex($colors[3]);

        $team->params()->update($data);
    }

    private function rgbToHex($rgb): string
    {
        return sprintf('#%02x%02x%02x', $rgb[0], $rgb[1], $rgb[2]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Team $team)
    {
        //
    }

    public function deleteLogo(Request $request, Team $team)
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

        $team->params()->update([
            'color1' => null,
            'color2' => null,
            'color3' => null,
            'color4' => null,
        ]);
    }

    public function switch(Team $team): \Illuminate\Http\JsonResponse|\Inertia\Response
    {
        if (! Gate::check('has-role-coordinateur') || ! config('teams.active')) {
            return Inertia::render('Errors/401');
        }

        Auth::user()->update(['team_id' => $team->id]);

        return response()->json(['url' => route('team.show', ['name' => $team->name])]);
    }

    public function getInformation(): \Inertia\Response
    {
        $team = Auth::user()->team;

        if (! config('teams.active') || ! $team) {
            return Inertia::render('Errors/404');
        }

        $roleNames = ['Conseiller', 'Coordinateur'];
        $users = User::whereHas('roles', function ($roleQuery) use ($roleNames) {
                $roleQuery->whereIn('name', $roleNames);
            })
            ->where('team_id', $team->id)
            ->get();
        $users = $users->reject(function ($item) use ($team) {
            return $item->isCoordinateur() && $team->user_id !== $item->id;
        });

        $links = null;

        if ($team->params->share_link) {
            $links = LinkTeam::where('team_id', $team->id)
                ->with(['user' => function ($query) {
                    $query->select('id', 'name');
                }])
                ->paginate(10);
        }

        return Inertia::render('Information/InformationTeam', [
            'usersProps' => $users,
            'linksProps' => $links,
        ]);
    }
}
