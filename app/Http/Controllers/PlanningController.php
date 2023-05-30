<?php

namespace App\Http\Controllers;

use App\Http\Requests\RequestGeneratePlanning;
use App\Models\Calendar;
use App\Models\Planning;
use Carbon\Carbon;
use Illuminate\Http\Request;

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

        Planning::where('user_id', $request->user)->delete();

        while (!$dateStart->eq($dateEnd)) {
            foreach ($request->rotations as $rotation) {
                foreach ($rotation['details'] as $detail) {
                    $this->createPlanning($detail, $rotation, $request->user, $dateStart);
                    $dateStart->addDay();

                    if ($dateStart->eq($dateEnd)) {
                        return;
                    }
                }
            }
        }
    }

    private function createPlanning($detail, $rotation, $userId, Carbon $date)
    {
        $calendar = Calendar::whereDate('date', $date)->firstOrFail();

        $planning = Planning::create([
            'type_day' => $detail['is_off'] ? 'Repos' : 'Travaille',
            'debut_journee' => str_replace('h', ':', $detail['debut_journee']),
            'debut_pause' => str_replace('h', ':', $detail['debut_pause']),
            'fin_pause' => str_replace('h', ':', $detail['fin_pause']),
            'fin_journee' => str_replace('h', ':', $detail['fin_journee']),
            'is_technician' => $detail['technicien'],
            'telework' => $detail['teletravail'],
            'calendar_id' => $calendar->id,
            'rotation_id' => $rotation['id'],
            'user_id' => $userId
        ]);

        return response()->json($planning);
    }
}
