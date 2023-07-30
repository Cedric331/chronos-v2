<?php

namespace App\Http\Controllers;

use App\Models\EventPlanning;
use App\Models\Planning;
use Illuminate\Http\Request;

class EventPlanningController extends Controller
{

    public function __construct()
    {
        $this->middleware('isCoordinateur');
    }

    public function store (Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'check' => 'nullable|boolean',
        ]);


        $eventPlanning = collect();
        foreach ($request->days as $day) {

            if ($request->check) {
                $plannings = Planning::where('team_id', $day['plannings'][0]['team_id'])
                    ->where('calendar_id', $day['plannings'][0]['calendar_id'])
                    ->get();

                foreach ($plannings as $planning) {
                    $event = EventPlanning::create([
                        'title' => $request->title,
                        'description' => $request->description,
                        'planning_id' => $planning->id,
                    ]);
                    $eventPlanning->push($event);
                }
            } else {
                $event = EventPlanning::create([
                    'title' => $request->title,
                    'description' => $request->description,
                    'planning_id' => $day['plannings'][0]['id'],
                ]);
                $eventPlanning->push($event);
            }
        }
        return response()->json($eventPlanning);
    }

    public function delete (Request $request)
    {
        $collection = collect();
        foreach ($request->days as $day) {
            $planning = Planning::findOrFail($day['plannings'][0]['id']);
            $planning->eventPlannings()->delete();
            $collection->push($planning);
        }

        return response()->json($collection);
    }
}
