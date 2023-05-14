<?php

namespace App\Http\Controllers;

use App\Models\Rotation;
use App\Models\RotationDetail;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RotationController extends Controller
{
    public function store (Request $request, Team $team) {

        DB::beginTransaction();
        try {
            $rotation = Rotation::create([
                'team_id' => $team->id,
                'name' => strtoupper($request->type)
            ]);

            foreach ($request->jours as $key => $jour) {
                if ($jour['is_off']) {
                    $debut_journee = null;
                    $debut_pause  = null;
                    $fin_pause = null;
                    $fin_journee = null;
                    $fin_journee = null;
                } else {
                    $debut_journee = $jour['debut_journee'] ? date("H:i:s", strtotime(str_replace('h', ':', $jour['debut_journee']))) : null;
                    $debut_pause = $jour['debut_pause'] ? date("H:i:s", strtotime(str_replace('h', ':', $jour['debut_pause']))) : null;
                    $fin_pause = $jour['fin_pause'] ? date("H:i:s", strtotime(str_replace('h', ':', $jour['fin_pause']))) : null;
                    $fin_journee = $jour['fin_journee'] ? date("H:i:s", strtotime(str_replace('h', ':', $jour['fin_journee']))) : null;
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
            }

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
                'name' => strtoupper($request->type)
            ]);

            // Supprimer les détails existants de la rotation
            $rotation->details()->delete();

            foreach ($request->jours as $key => $jour) {
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
            }

            DB::commit();
            return response()->json(Rotation::with('details')->get());
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

}
