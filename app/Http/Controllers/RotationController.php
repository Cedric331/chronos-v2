<?php

namespace App\Http\Controllers;

use App\Http\Requests\RotationRequest;
use App\Models\Rotation;
use App\Models\RotationDetail;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class RotationController extends Controller
{
    public function store (RotationRequest $request, Team $team): \Illuminate\Http\JsonResponse|\Inertia\Response
    {
        if (!$request->ajax()) {
            return Inertia::render('Errors/404');
        }

        DB::beginTransaction();
        try {
            $rotation = Rotation::create([
                'team_id' => $team->id,
                'name' => strtoupper($request->name)
            ]);

            $totalHours = 0;
            foreach ($request->jours as $key => $jour) {
                if ($jour['debut_pause'] === 'Pas de pause') {
                    $jour['debut_pause'] = null;
                    $jour['fin_pause'] = null;
                }
                $hours = 0;
                if ($jour['is_off']) {
                    $debut_journee = null;
                    $debut_pause  = null;
                    $fin_pause = null;
                    $fin_journee = null;
                } else {
                    $debut_journee = $jour['debut_journee'] ? date("H:i:s", strtotime(str_replace('h', ':', $jour['debut_journee']))) : null;
                    $debut_pause = $jour['debut_pause'] ? date("H:i:s", strtotime(str_replace('h', ':', $jour['debut_pause']))) : null;
                    $fin_pause = $jour['fin_pause'] ? date("H:i:s", strtotime(str_replace('h', ':', $jour['fin_pause']))) : null;
                    $fin_journee = $jour['fin_journee'] ? date("H:i:s", strtotime(str_replace('h', ':', $jour['fin_journee']))) : null;

                    if ($debut_journee && $fin_journee) {
                        $startTime = strtotime($debut_journee);
                        $endTime = strtotime($fin_journee);

                        if ($debut_pause && $fin_pause) {
                            $pauseStartTime = strtotime($debut_pause);
                            $pauseEndTime = strtotime($fin_pause);

                            $workHours = ($endTime - $startTime) - ($pauseEndTime - $pauseStartTime); // Soustraire la durée de la pause
                        } else {
                            $workHours = ($endTime - $startTime);
                        }

                        $hours = $workHours;
                    }
                }

                RotationDetail::create([
                    'day' => $key,
                    'is_off' => $jour['is_off'],
                    'debut_journee' => $debut_journee,
                    'debut_pause' => $debut_pause,
                    'fin_pause' => $fin_pause,
                    'fin_journee' => $fin_journee,
                    'technicien' => !$jour['is_off'] ? $jour['technicien'] : false,
                    'teletravail' => !$jour['is_off'] ? $jour['teletravail'] : false,
                    'rotation_id' => $rotation->id
                ]);
                $totalHours += $hours;
            }

            $totalHoursInHours = $totalHours / 3600;

            $hours = floor($totalHoursInHours);

            $minutes = ($totalHoursInHours - $hours) * 60;

            $rotation->total_hours = sprintf("%02dh%02d", $hours, $minutes);
            $rotation->save();

            DB::commit();
            return response()->json(Rotation::with('details')->get());

        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }


    public function update(RotationRequest $request, Rotation $rotation): \Illuminate\Http\JsonResponse|\Inertia\Response
    {
        if (!$request->ajax()) {
            return Inertia::render('Errors/404');
        }

        DB::beginTransaction();
        try {
            $rotation->update([
                'name' => strtoupper($request->name)
            ]);

            $rotation->details()->delete();
            $totalHours = 0;

            foreach ($request->jours as $key => $jour) {

                $hours = 0;

                if ($jour['is_off']) {
                    $debut_journee = null;
                    $debut_pause  = null;
                    $fin_pause = null;
                    $fin_journee = null;
                } else {
                    if ($jour['debut_pause'] === 'Pas de pause') {
                        $jour['debut_pause'] = null;
                        $jour['fin_pause'] = null;
                    }
                    $debut_journee = $jour['debut_journee'] ? date("H:i:s", strtotime(str_replace('h', ':', $jour['debut_journee']))) : null;
                    $debut_pause = $jour['debut_pause'] ? date("H:i:s", strtotime(str_replace('h', ':', $jour['debut_pause']))) : null;
                    $fin_pause = $jour['fin_pause'] ? date("H:i:s", strtotime(str_replace('h', ':', $jour['fin_pause']))) : null;
                    $fin_journee = $jour['fin_journee'] ? date("H:i:s", strtotime(str_replace('h', ':', $jour['fin_journee']))) : null;

                    if ($debut_journee && $fin_journee) {
                        $startTime = strtotime($debut_journee);
                        $endTime = strtotime($fin_journee);

                        if ($debut_pause && $fin_pause) {
                            $pauseStartTime = strtotime($debut_pause);
                            $pauseEndTime = strtotime($fin_pause);

                            $workHours = ($endTime - $startTime) - ($pauseEndTime - $pauseStartTime);
                        } else {
                            $workHours = ($endTime - $startTime);
                        }

                        $hours = $workHours;
                    }
                }

                $rotation->details()->create([
                    'day' => strtolower($key),
                    'is_off' => $jour['is_off'],
                    'debut_journee' => $debut_journee,
                    'debut_pause' => $debut_pause,
                    'fin_pause' => $fin_pause,
                    'fin_journee' => $fin_journee,
                    'technicien' => !$jour['is_off'] ? $jour['technicien'] : false,
                    'teletravail' => !$jour['is_off'] ? $jour['teletravail'] : false,
                ]);
                $totalHours += $hours;
            }

            $totalHoursInHours = $totalHours / 3600;

            $hours = floor($totalHoursInHours);

            $minutes = ($totalHoursInHours - $hours) * 60;

            $rotation->total_hours = sprintf("%02dh%02d", $hours, $minutes);
            $rotation->save();

            DB::commit();
            return response()->json(Rotation::with('details')->get());
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

}
