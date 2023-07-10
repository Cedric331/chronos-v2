<?php

namespace App\Http\Controllers;

use App\Models\AlertSchedule;
use App\Models\Team;
use App\Models\TeamSchedule;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TeamScheduleController extends Controller
{
    public function __construct()
    {
        $this->middleware('isCoordinateur');
    }

    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        $team_id = Auth::user()->team_id;
        $data = $request->all();

        $response = [];

        foreach ($data as $item) {
            if ($item['team_id'] != $team_id) {
                return response()->json(['error' => 'Vous ne pouvez pas modifier les alertes d\'horaire d\'une autre équipe'], 400);
            }
            $validator = Validator::make($item, [
                'day' => 'required|string',
                'time' => 'required|string',
                'value' => 'nullable|integer',
                'team_id' => 'required|integer',
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 400);
            }

            [$start, $end] = explode(' - ', $item['time']);

            $start = Carbon::createFromFormat('G\h', $start)->format('H:i:s');
            $end = Carbon::createFromFormat('G\h', $end)->format('H:i:s');

            $convertedTime = "$start - $end";

            $teamSchedule = TeamSchedule::updateOrCreate([
                'day' => $item['day'],
                'time' => $convertedTime,
                'team_id' => $item['team_id'],
            ], [
                'value' => $item['value'],
            ]);

            $response[] = $teamSchedule;
        }

        return response()->json([
            'message' => 'Information modifié avec succès',
            'team_schedules' => $response,
        ], 201);
    }

    public function readOne(AlertSchedule $alert)
    {
        $alert->markAsRead();

        return response()->json([
            'message' => 'Notification marquée comme lue',
        ], 200);
    }

    public function read()
    {
        $team = Team::find(Auth::user()->team_id);

        $team->alerts()->each(function ($alert) {
            $alert->markAsRead();
        });

        return response()->json([
            'message' => 'Notifications marquées comme lues',
        ], 200);
    }
}
