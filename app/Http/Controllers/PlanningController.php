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

    private function createPlanning($detail, $rotation, $userId, Carbon $date): \Illuminate\Http\JsonResponse
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

    public function update (Request $request)
    {
        $ids = array_column($request->days, 'id');
        foreach ($request->days as $day) {
            $planning = Planning::find($day['plannings'][0]['id']);
            if ($this->checkTypeDay($request->type_day)) {
                $planning->update([
                    'type_day' => $request->type_day,
                    'debut_journee' => $request->debut_journee,
                    'debut_pause' => $request->debut_pause,
                    'fin_pause' => $request->fin_pause,
                    'fin_journee' => $request->fin_journee,
                    'is_technician' => $request->is_technician,
                    'telework' => $request->telework,
                ]);
            } else {
                $planning->update([
                    'type_day' => $request->type_day,
                    'debut_journee' => null,
                    'debut_pause' => null,
                    'fin_pause' => null,
                    'fin_journee' => null,
                    'is_technician' => false,
                    'telework' => false,
                ]);
            }
        }

        $calendar = Calendar::with(['plannings' => function ($query) use ($planning) {
                $query->where('user_id', $planning->user_id);
            }])
            ->find($ids);

        return response()->json($calendar);
    }

    private function checkTypeDay ($typeDay)
    {
        if ($typeDay === 'Formation' || $typeDay === 'Planifié') {
            return true;
        }
        return false;
    }
}
