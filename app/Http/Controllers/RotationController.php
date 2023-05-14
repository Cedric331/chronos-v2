<?php

namespace App\Http\Controllers;

use App\Http\Requests\RotationRequest;
use App\Models\Rotation;
use App\Models\RotationDetail;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RotationController extends Controller
{
    public function store (RotationRequest $request, Team $team): \Illuminate\Http\JsonResponse
    {

        DB::beginTransaction();
        try {
            $rotation = Rotation::create([
                'team_id' => $team->id,
                'name' => strtoupper($request->name)
            ]);

            $totalHours = 0;
            foreach ($request->jours as $key => $jour) {
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

                        $hours = $workHours / 3600; // Convertir en heures
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

            $rotation->total_hours = $totalHours;
            DB::commit();
            return response()->json(Rotation::with('details')->get());

        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function update(Request $request, Rotation $rotation)
    {
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

                        $hours = $workHours / 3600; // Convertir en heures
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

            $rotation->total_hours = $totalHours;

            DB::commit();
            return response()->json(Rotation::with('details')->get());
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

}
