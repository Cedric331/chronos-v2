<?php

namespace App\Http\Controllers;

use App\Http\Requests\RequestGeneratePlanning;
use App\Models\Calendar;
use App\Models\Planning;
use App\Models\Rotation;
use App\Models\User;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PlanningController extends Controller
{
    public function __construct()
    {
        $this->middleware('isCoordinateur')->only('generate');
    }

    /**
     * @param RequestGeneratePlanning $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function generate(RequestGeneratePlanning $request): \Illuminate\Http\JsonResponse
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

    private function createPlanning($detail, $rotation, $userId, Carbon $date): void
    {
        $calendar = Calendar::whereDate('date', $date)->first();

        if ($calendar) {
            $planning = Planning::create([
                'type_day' => $detail['is_off'] ? 'Repos' : 'Planifié',
                'debut_journee' => $detail['debut_journee'],
                'debut_pause' => $detail['debut_pause'],
                'fin_pause' => $detail['fin_pause'],
                'fin_journee' => $detail['fin_journee'],
                'is_technician' => $detail['technicien'],
                'telework' => $detail['teletravail'],
                'hours' => $this->calculateWorkHours($detail),
                'calendar_id' => $calendar->id,
                'rotation_id' => $rotation['id'],
                'team_id' => Auth::user()->team_id,
                'user_id' => $userId
            ]);


            response()->json($planning);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update (Request $request): \Illuminate\Http\JsonResponse
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
                    'hours' => $this->calculateWorkHours($request),
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
                    'hours' => null,
                ]);
            }
        }

        $calendar = Calendar::with(['plannings' => function ($query) use ($planning) {
                $query->where('user_id', $planning->user_id);
            }])->find($ids);

        return response()->json($calendar);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateWithRotation (Request $request): \Illuminate\Http\JsonResponse
    {
        $rotation = Rotation::find($request->rotation_id);
        $ids = array_column($request->days, 'id');
        foreach ($request->days as $day) {
            $planning = Planning::find($day['plannings'][0]['id']);
                $day = strtolower($planning->calendar->getDay());
                foreach ($rotation->details as $detail) {
                    if (strtolower($detail['day']) === $day) {

                        $planning->update([
                            'debut_journee' => $detail->debut_journee,
                            'debut_pause' => $detail->debut_pause,
                            'fin_pause' => $detail->fin_pause,
                            'fin_journee' => $detail->fin_journee,
                            'telework' => $detail->teletravail,
                            'is_technician' => $detail->technicien,
                            'rotation_id' => $rotation->id,
                            'type_day' => $detail->is_off ? 'Repos' : 'Planifié',
                        ]);

                }
            }
        }

        $calendar = Calendar::with(['plannings' => function ($query) use ($planning) {
            $query->where('user_id', $planning->user_id);
        }])
            ->find($ids);

        return response()->json($calendar);
    }

    public function getPlanningTeam(Request $request): \Illuminate\Http\JsonResponse
    {
        $user = User::find(Auth::id());
        $day = Calendar::find($request->day_id);
        $date = Carbon::parse($day->date);

        $startOfWeek = $date->copy()->startOfWeek(Carbon::MONDAY);
        $endOfWeek = $date->copy()->endOfWeek(Carbon::SUNDAY);

        if ($request->mobile) {
            $calendar = Calendar::whereHas('plannings', function ($query) use ($user) {
                $query->where('team_id', $user->team_id);
            })
                ->with(['plannings' => function ($query) use ($user, $date) {
                    $query->where('team_id', $user->team_id);
                }, 'plannings.user'])
                ->where('date', '=', $date)
                ->get();
        } else {
            $calendar = User::where('team_id', $user->team_id)
                ->whereHas('plannings', function ($query) use ($startOfWeek, $endOfWeek) {
                    $query->whereHas('calendar', function ($query) use ($startOfWeek, $endOfWeek) {
                        $query->where('date', '>=', $startOfWeek)
                            ->where('date', '<=', $endOfWeek);
                    });
                })
                ->with(['plannings' => function ($query) use ($startOfWeek, $endOfWeek) {
                    $query->whereHas('calendar', function ($query) use ($startOfWeek, $endOfWeek) {
                        $query->where('date', '>=', $startOfWeek)
                            ->where('date', '<=', $endOfWeek);
                    });
                }, 'plannings.calendar'])
                ->get();
        }

        return response()->json($calendar);
    }

    /**
     * @param $typeDay
     * @return bool
     */
    private function checkTypeDay ($typeDay): bool
    {
        if ($typeDay === 'Formation' || $typeDay === 'Planifié') {
            return true;
        }
        return false;
    }


    /**
     * @param $workDay
     * @return string|null
     */
    private function calculateWorkHours($workDay): ?string
    {
        $format = "H:i";
        if ($workDay['is_off'] || !$workDay['debut_journee']) {
            return null;
        }

        $startDayTime = is_array($workDay) ? str_replace('h', ':', $workDay['debut_journee']) : str_replace('h', ':', $workDay->debut_journee);
        $endDayTime = is_array($workDay) ? str_replace('h', ':', $workDay['fin_journee']) : str_replace('h', ':', $workDay->fin_journee);

        $startDay = DateTime::createFromFormat($format, $startDayTime);
        $endDay = DateTime::createFromFormat($format, $endDayTime);

        $totalWorkHours = $endDay->diff($startDay)->h + ($endDay->diff($startDay)->i / 60);

        if (isset($workDay['debut_pause']) && isset($workDay['fin_pause'])) {
            $startBreakTime = is_array($workDay) ? str_replace('h', ':', $workDay['debut_pause']) : str_replace('h', ':', $workDay->debut_pause);
            $endBreakTime = is_array($workDay) ? str_replace('h', ':', $workDay['fin_pause']) : str_replace('h', ':', $workDay->fin_pause);

            if ($startBreakTime !== null && $endBreakTime !== null) {
                $startBreak = DateTime::createFromFormat($format, $startBreakTime);
                $endBreak = DateTime::createFromFormat($format, $endBreakTime);

                if ($startBreak && $endBreak) {
                    $breakHours = $endBreak->diff($startBreak)->h + ($endBreak->diff($startBreak)->i / 60);
                    $totalWorkHours -= $breakHours;
                }
            }
        }

        $hours = intval($totalWorkHours);
        $minutes = ($totalWorkHours - $hours) * 60;

        return sprintf("%02d", $hours) . 'h' . sprintf("%02d", $minutes);
    }

}
