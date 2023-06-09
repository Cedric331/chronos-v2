<?php

namespace App\Http\Controllers;

use App\Http\Requests\RequestGeneratePlanning;
use App\Models\Calendar;
use App\Models\Planning;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PlanningController extends Controller
{
    public function __construct()
    {
        $this->middleware('isCoordinateur')->only('generate');
    }

    public function generate(RequestGeneratePlanning $request)
    {
        $dateStart = Carbon::parse($request->dateStart);
        $dateEnd = Carbon::parse($request->dateEnd)->addWeek();

        if ($dateStart->gt($dateEnd)) {
            return response()->json('Erreur dans la sélection des dates', 422);
        }

        // TODO Gérer les plannings déjà planifié (CP, RJF...)
        Planning::where('user_id', $request->user)->delete();

        $countDayGenerate = 0;
        while (!$dateStart->eq($dateEnd)) {
            foreach ($request->rotations as $rotation) {
                foreach ($rotation['details'] as $detail) {
                    $this->createPlanning($detail, $rotation, $request->user, $dateStart);
                    $dateStart->addDay();
                    $countDayGenerate++;

                    if ($dateStart->eq($dateEnd)) {
                        return response()->json($countDayGenerate);
                    }
                }
            }
        }
        return response()->json('Erreur', 422);
    }

    private function createPlanning($detail, $rotation, $userId, Carbon $date)
    {
        $calendar = Calendar::whereDate('date', $date)->firstOrFail();

        $planning = Planning::create([
            'type_day' => $detail['is_off'] ? 'Repos' : 'Planifié',
            'debut_journee' => str_replace('h', ':', $detail['debut_journee']),
            'debut_pause' => $detail['debut_pause'] !== null ? str_replace('h', ':', $detail['debut_pause']) : null,
            'fin_pause' => $detail['fin_pause'] !== null ? str_replace('h', ':', $detail['fin_pause']) : null,
            'fin_journee' => str_replace('h', ':', $detail['fin_journee']),
            'is_technician' => $detail['technicien'],
            'telework' => $detail['teletravail'],
            'calendar_id' => $calendar->id,
            'rotation_id' => $rotation['id'],
            'team_id' => Auth::user()->team_id,
            'user_id' => $userId
        ]);

        return response()->json($planning);
    }
}
